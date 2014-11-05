<?php

message("Init");
require_once('init.php');

$gt = microtime(true);

Application::import(PATH_APPLICATION . '/model/models/GameSettingsModel.php');
Application::import(PATH_APPLICATION . '/model/models/TicketsModel.php');
Application::import(PATH_APPLICATION . '/model/entities/Lottery.php');

messageLn(" [done]");

message("Get settings");
$time = microtime(true);
$gameSettings = GameSettingsModel::instance()->loadSettings();
$gamePrizes   = $gameSettings->getPrizes();
messageLn(" [done]  -> " . number_format((microtime(true) - $time),3) . " s.");

$lockFile = dirname(__FILE__) . '/lottery.lock';
$lockTimeout = 60 * 3;

$_ballsCount = 6;
$_variantsCount = 49;

ini_set('memory_limit', -1);

if (isLocked()) {
    die("Locked by previous execution" . PHP_EOL);
}
messageLn("Start lottery");
if (timeToRunLottery()) {
    setLock();

    message("Get tickets");
    $time = microtime(true);
    // get players tickets    
    $tickets = TicketsModel::instance()->getAllUnplayedTickets();
    $lotteryCombination = array();
    messageLn(" [done]  -> " . number_format((microtime(true) - $time),3) . " s.");
    messageLn("Tickets count - " . count($tickets));

    messageLn("Generation (" . Config::instance()->generatorNumTries . " tries)");
    $tgt = microtime(true);
    // if need to play jackpot
    
    if ($gameSettings->getJackpot()) {
        $winner = array_rand($tickets);
        $lotteryCombination = $tickets[$winner]->getCombination(); 
    } else {
        $lotteryCombinations = array();
        for ($i = 0; $i < Config::instance()->generatorNumTries; ++$i) {
            $combination = array();

            while (count($combination) < $_ballsCount ) {
                $rand = mt_rand(1, $_variantsCount);

                if (!in_array($rand, $combination)) {
                    $combination[] = $rand;
                }
            }
            $lotteryCombinations[] = $combination;
        }
        message("   Sorting bets");
        $time = microtime(true);
        $bets = array();
        foreach ($tickets as $ticket) {
            foreach ($ticket->getCombination() as $num) {
                @$bets[$num]++;
            }
        }
        $time = microtime(true);
        messageLn("    [done]  -> " . number_format((microtime(true) - $tgt),3) . " s.");

        // get most better combination
        $maxWin = 0;
        $lotteryCombination = array();
        foreach ($lotteryCombinations as $id => $combination) {
            $combinationWin = 0;
            foreach ($combination as $combinationNum) {
                foreach ($bets as $num => $bet) {
                    if ($combinationNum == $num) {
                        $combinationWin += $bet;
                    }
                }
            }
            if ($combinationWin > $maxWin) {
                $maxWin = $combinationWin;

                $lotteryCombination = $lotteryCombinations[$id];
            }
        }
    }
    messageLn("[done]  -> " . number_format((microtime(true) - $time),2) . " s.");

    message("Compare tickets");
    $time = microtime(true);
    $playersPlayed  = array();
    $pointsWonTotal = 0;
    $moneyWonTotal  = 0;

    foreach ($tickets as $ticket) {
        $compares = 0;
        foreach ($ticket->getCombination() as $ticketBet) {
            foreach ($lotteryCombination as $lotteryBet) {
                if ($ticketBet == $lotteryBet) {
                    $compares++;
                }
            }
        }
        // ticket win
        if ($compares > 0) {
            // calculate point or UA money total
            if ($gamePrizes['UA'][$compares]['currency'] == GameSettings::CURRENCY_MONEY) {
                $moneyWonTotal += $gamePrizes['UA'][$compares]['sum'];                    
            } else {
                $pointsWonTotal += $gamePrizes['UA'][$compares]['sum'];
            }
            // compile players and tickets
            if (!isset($playersPlayed[$ticket->getPlayerId()])) {
                $playersPlayed[$ticket->getPlayerId()] = array(
                    'tickets'   => array(),
                );
            }
            $playersPlayed[$ticket->getPlayerId()]['tickets'][$ticket->getId()] = $compares;
        }
    }

    messageLn(" [done]  -> " . number_format((microtime(true) - $time),2) . " s.");
    message("Get players country setting");
    $time = microtime(true);

    $playersCountry = array();
    DB::Connect()->query(sprintf("SELECT `Id`, `Country` FROM `Players` WHERE `Id` IN (%s)", join(",", array_keys($playersPlayed))))->fetchAll(PDO::FETCH_FUNC, function($plid, $country) use (&$playersCountry) {
            $playersCountry[$plid] = $country;
            return $country;
        });

    messageLn(" [done]  -> " . number_format((microtime(true) - $time),2) . " s.");
    messageLn("");

    message("Storing lottery data");
    $time = microtime(true);
    // create lottery instance;
    $lottery = new Lottery();
    $lottery->setCombination($lotteryCombination)
            ->setWinnersCount(count($playersPlayed))
            ->setMoneyTotal($moneyWonTotal)
            ->setPointsTotal($pointsWonTotal);

    try {
        $lottery->create();
    } catch (EntityException $e) {
        echo "Something gone wrong and i cant store lottery data" . PHP_EOL;
        echo $e->getMessage() . PHP_EOL;

        releaseLock();

        exit;
    }
    messageLn(" [done]  -> " . number_format((microtime(true) - $time),2) . " s.");

    message("Calculations and data preparing");
    $time = microtime(true);
    $queries = array(
        'transactions' => "INSERT INTO `Transactions` (`PlayerId`, `Currency`, `Sum`, `Description`, `Date`) VALUES %s",
        'players'      => "UPDATE `Players` SET `GamesPlayed`=`GamesPlayed`+1,`Money`=CASE %s END,`Points`=CASE %s END WHERE `Id` IN (%s)",
        'tickets' => "UPDATE `LotteryTickets` SET `TicketWin`=CASE %s END, `TicketWinCurrency`=CASE %s END WHERE `Id` IN(%s)",
        'lotteryWins' => "INSERT INTO `PlayerLotteryWins` (`LotteryId`, `PlayerId`, `Date`, `MoneyWin`, `PointsWin`) VALUES %s",
        'ticketsUpdate' => sprintf("UPDATE `LotteryTickets` SET `LotteryId` = %s WHERE `LotteryId` = 0", DB::Connect()->quote($lottery->getId())),

    );
    $transactionsSql = $playersMoneySql = $playersPointsSql = $ticketsSumSql = $ticketsCurrencySql = $lotteryWinSql = array();
    $ticketIds = array();
    foreach ($playersPlayed as $playerId => $data) {
        $playerPoints = $playerMoney = 0;
        foreach ($data['tickets'] as $ticketId => $ticketCompare) {
            $ticketIds[] = $ticketId;
            // get player country
            $pcountry = $playersCountry[$playerId];
            if (!in_array($pcountry, Config::instance()->langs)) {
                $pcountry = Config::instance()->defaultLang;
            }

            $win = $gamePrizes[$pcountry][$ticketCompare]['sum'];
            $currency = $gamePrizes[$pcountry][$ticketCompare]['currency'];

            if ($currency == GameSettings::CURRENCY_MONEY) {
                $playerMoney += $win; 
            } else {
                $playerPoints += $win;
            }
            $ticketsSumSql[] = vsprintf("WHEN `Id`=%s THEN %s", array(
                DB::Connect()->quote($ticketId),
                DB::Connect()->quote($win),
            ));
            $ticketsCurrencySql[] = vsprintf("WHEN `Id`=%s THEN %s", array(
                DB::Connect()->quote($ticketId),
                DB::Connect()->quote($currency),
            ));
            // update ticket data
        }
        // create transactions
        if ($playerMoney > 0) {
            $transactionsSql[] = vsprintf("(%s,%s,%s,%s,%s)", array(
                DB::Connect()->quote($playerId),
                DB::Connect()->quote(GameSettings::CURRENCY_MONEY),
                DB::Connect()->quote($playerMoney),
                DB::Connect()->quote("Выигрыш в розыгрыше"),
                DB::Connect()->quote(time()),
            ));
        }
        if ($playerPoints > 0) {
            $transactionsSql[] = vsprintf("(%s,%s,%s,%s,%s)", array(
                DB::Connect()->quote($playerId),
                DB::Connect()->quote(GameSettings::CURRENCY_POINT),
                DB::Connect()->quote($playerPoints),
                DB::Connect()->quote("Выигрыш в розыгрыше"),
                DB::Connect()->quote(time()),
            ));   
        }
        $playersMoneySql[] = vsprintf("WHEN `Id`=%s THEN `Money`+%s", array(
            DB::Connect()->quote($playerId),
            DB::Connect()->quote($playerMoney),
        ));
        $playersPointsSql[] = vsprintf("WHEN `Id`=%s THEN `Points`+%s", array(
            DB::Connect()->quote($playerId),
            DB::Connect()->quote($playerPoints),
        ));
        //`LotteryId`, `PlayerId`, `Date`, `MoneyWin`, `PointsWin`
        $lotteryWinSql[] = vsprintf("(%s,%s,%s,%s,%s)", array(
            DB::Connect()->quote($lottery->getId()),
            DB::Connect()->quote($playerId),
            DB::Connect()->quote(time()),
            DB::Connect()->quote($playerMoney),
            DB::Connect()->quote($playerPoints),
        ));
    }
    $queries['transactions'] = sprintf($queries['transactions'], join(',', $transactionsSql));
    $queries['players'] = sprintf($queries['players'], join(" ", $playersMoneySql), join(" ", $playersPointsSql), join(",", array_keys($playersPlayed)));
    $queries['tickets'] = sprintf($queries['tickets'], join(" ", $ticketsSumSql), join(" ", $ticketsCurrencySql), join(",", $ticketIds));
    
    $queries['lotteryWins'] = sprintf($queries['lotteryWins'], join(',', $lotteryWinSql));
    messageLn(" [done]  -> " . number_format((microtime(true) - $time),2) . " s.");

    message("Storing data");
    $time = microtime(true);
    try {
        foreach ($queries as $query) {
            DB::Connect()->query($query);
        }
    } catch (PDOException $e) {
        messageLn($e->getMessage());
    }
    messageLn(" [done]  -> " . number_format((microtime(true) - $time),2) . " s.");

    $lottery->publish();

    messageLn("");
    echo "Players won count " . count($playersPlayed) . PHP_EOL;

    echo "Money total " . $moneyWonTotal . PHP_EOL;
    echo "Points total " . $pointsWonTotal . PHP_EOL;

    releaseLock();
} else {
    exit;
}
messageLn("");
messageLn("Total time s  -> " . number_format((microtime(true) - $gt),2) . " s.");
function timeToRunLottery() {
    global $gameSettings;

    if (@$_SERVER['argv'][1] == 'dbg') {
        return true;
    }

    $currentTime = strtotime(date('H:i'), 0);

    foreach ($gameSettings->getGameTimes() as $time) {
        if ($currentTime == $time) {
            return true;
        }
    }

    return false;
}

function getLock()
{
    global $lockFile;

    if (!is_file($lockFile)) {
        return false;
    }

    return file_get_contents($lockFile);
}

function setLock() 
{
    global $lockFile;

    file_put_contents($lockFile, time());
}

function releaseLock()
{
    global $lockFile;

    unlink($lockFile);
}

function isLocked() 
{
    global $lockTimeout;

    if (!($lockTime = getLock())) {
        return false;
    }

    if ($lockTime + $lockTimeout < time()) {
        releaseLock();

        return false;
    }

    return true;
}

function message($message) {
    echo $message;
}

function messageLn($message) {
    message($message);

    echo PHP_EOL;
}
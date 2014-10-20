<?php

require_once('init.php');

Application::import(PATH_APPLICATION . '/model/models/GameSettingsModel.php');
Application::import(PATH_APPLICATION . '/model/models/TicketsModel.php');
Application::import(PATH_APPLICATION . '/model/Entities/Lottery.php');

$gameSettings = GameSettingsModel::instance()->loadSettings();
$gamePrizes   = $gameSettings->getPrizes(Config::instance()->defaultLang);

$lockFile = dirname(__FILE__) . '/lottery.lock';
$lockTimeout = 60 * 3;

$_ballsCount = 6;
$_variantsCount = 49;

ini_set('memory_limit', -1);

if (isLocked()) {
    die("Locked by previous execution" . PHP_EOL);
}

if (timeToRunLottery()) {
    setLock();

    // get players tickets    
    $tickets = TicketsModel::instance()->getAllUnplayedTickets();
    $lotteryCombination = array();

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

        // get most better combination
        $maxWin = 0;
        $lotteryCombination = array();
        foreach ($lotteryCombinations as $id => $combination) {
            $combinationWin = 0;
            foreach ($combination as $combinationNum) {
                foreach ($tickets as $ticket) {
                    foreach ($ticket->getCombination() as $ticketNum) {
                        if ($combinationNum == $ticketNum) {
                            $combinationWin++;
                        }
                    }
                }
            }

            if ($combinationWin > $maxWin) {
                $maxWin = $combinationWin;

                $lotteryCombination = $lotteryCombinations[$id];
            }
        }
    }

    $pointsWonTotal = 0;
    $moneyWonTotal  = 0;
    $playersPlayed  = array();
    $playersWon     = array();
    $playersWonTickets = array();
    $playersWonMoney = array();
    $playerPrizes   = array();
    $ticketsWon = array();

    if (count($tickets)) {
        foreach ($tickets as $ticket) {
            // add player
            if (!isset($playersPlayed[$ticket->getPlayerId()])) {
                $player = new Player();
                $player->setId($ticket->getPlayerId())->fetch();
                $playersPlayed[$player->getId()] = $player;

                unset($player);
            }
            $numwins = 0;
            // check ticket combination compared to lottery combination
            foreach ($lotteryCombination as $lc) {
                foreach ($ticket->getCombination() as $tc) {
                    if ($tc == $lc) {
                        $numwins++;
                    }
                }
            }
            // if ticket have won store it
            if ($numwins > 0) {
                if (!in_array($ticket->getPlayerId(), $playersWon)) {
                    $playersWon[] = $ticket->getPlayerId();
                }
                $playersWonTickets[$ticket->getPlayerId()][$ticket->getId()] = array(
                    'ticket' => $ticket,
                    'win'    => $numwins,
                );

                // save some statistic
                if ($gamePrizes[$numwins]['currency'] == GameSettings::CURRENCY_MONEY) {
                    $moneyWonTotal += $gamePrizes[$numwins]['sum'];
                    if (!in_array($ticket->getPlayerId(), $playersWonMoney)) {
                        $playersWonMoney[] = $ticket->getPlayerId();
                    }
                } else {
                    $pointsWonTotal += $gamePrizes[$numwins]['sum'];
                }
            }
        }
    }

    // create lottery instance;
    $lottery = new Lottery();
    $lottery->setCombination($lotteryCombination)
            ->setWinnersCount(count($playersWonMoney))
            ->setMoneyTotal($moneyWonTotal)
            ->setPointsTotal($pointsWonTotal);

    try {
        $lottery->create();
    } catch (EntityException $e) {
        echo "Something gone wrong and i cant store lottery data" . PHP_EOL;

        releaseLock();

        exit;
    }

    // update players data
    foreach ($playersPlayed as $player) {
        $player->setGamesPlayed($player->getGamesPlayed() + 1);
        if (in_array($player->getId(), $playersWon)) {            
            $moneyToAdd = $pointsToAdd = 0;
            $playerPrizes[$player->getId()] = array(
                'money' => 0,
                'points' => 0,
            );
            foreach ($playersWonTickets[$player->getId()] as $ticketData) {
                if ($gamePrizes[$ticketData['win']]['currency'] == GameSettings::CURRENCY_MONEY) {
                    $moneyToAdd += $gamePrizes[$ticketData['win']]['sum'];
                } else {
                    $pointsToAdd += $gamePrizes[$ticketData['win']]['sum'];
                }
            }
            if ($moneyToAdd > 0)  {
                $playerPrizes[$player->getId()]['money'] = $moneyToAdd;
                $player->addMoney($moneyToAdd, 'Выигшрыш в лотерее');
            }

            if ($pointsToAdd > 0) {
                $playerPrizes[$player->getId()]['points'] = $pointsToAdd;
                $player->addPoints($pointsToAdd, 'Выигшрыш в лотерее');
            }
        }
        $player->update();
    }
    // update tickets wins
    foreach ($playersWonTickets as $playerId => $playerData) {
        foreach ($playerData as $ticketId => $data) {
            DB::Connect()->prepare("UPDATE `LotteryTickets` SET `TicketWin` = :tw, `TicketWinCurrency` = :twc WHERE `Id` = :tid")->execute(array(
                ':tw' => $gamePrizes[$data['win']]['sum'],
                ':twc' => $gamePrizes[$data['win']]['currency'],
                ':tid' => $ticketId,
            ));
        }
    }

    // mark player lottery tickets as played
    DB::Connect()->prepare("UPDATE `LotteryTickets` SET `LotteryId` = :li WHERE `LotteryId` = 0")->execute(array(
        ':li'   => $lottery->getId(),
    ));

    foreach ($playersPlayed as $player) {
        $sql = "INSERT INTO `PlayerLotteryWins` (`LotteryId`, `PlayerId`, `Date`, `MoneyWin`, `PointsWin`) VALUES (:lotid, :plid, :date, :mw, :pw)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':lotid' => $lottery->getId(),
                ':plid'  => $player->getId(),
                ':date'  => $lottery->getDate(),
                ':mw'    => @$playerPrizes[$player->getId()]['money'] ?: 0,
                ':pw'    => @$playerPrizes[$player->getId()]['points'] ?: 0,
            ));
        } catch (PDOException $e) {}
    }

    $lottery->publish();

    echo "Players won count " . count($playersWon) . PHP_EOL;

    echo "Money total " . $moneyWonTotal . PHP_EOL;
    echo "Points total " . $pointsWonTotal . PHP_EOL;

    releaseLock();
} else {
    exit;
}

function timeToRunLottery() {
    global $gameSettings;

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

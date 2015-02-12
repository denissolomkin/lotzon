<?php


    echo PHP_EOL.'************ '.date('H:i:s').' ************'.PHP_EOL;


	require_once('lottery.inc.php');

	if(timeToRunLottery())
	{
		if(!file_exists($tmp = __DIR__.'/lottery.lock.tmp'))
		{
			file_put_contents($tmp, '');

            HoldLotteryAndCheck(0, 40, 50);

            unlink($tmp);
		}
        else
        {
            echo $tmp.' is exists'.PHP_EOL;
        }
	}
    else
    {
        echo 'timeToRunLottery is false'.PHP_EOL;
    }



/*
message("Init");

require_once('init.php');

ini_set('memory_limit', -1);


global $_ballsCount;    $_ballsCount    = 6;
global $_variantsCount; $_variantsCount = 49;


$gt = microtime(true);

Application::import(PATH_APPLICATION . '/model/models/GameSettingsModel.php');
Application::import(PATH_APPLICATION . '/model/models/TicketsModel.php');
Application::import(PATH_APPLICATION . '/model/entities/Lottery.php');

//recompile();
messageLn(" [done] -> " . number_format((microtime(true) - $gt),3) . " s.");

message("Get settings");
$time = microtime(true);
$gameSettings = GameSettingsModel::instance()->loadSettings();
$gamePrizes   = $gameSettings->getPrizes();
messageLn(" [done]  -> " . number_format((microtime(true) - $time),3) . " s.");

$lockFile = dirname(__FILE__) . '/lottery.lock';
$lockTimeout = 60 * 5;

if (isLocked()) {
    die("Locked by previous execution" . PHP_EOL);
}

messageLn("Start lottery");
if (timeToRunLottery()) {
    setLock();

    message("   Get tickets");
    $time = microtime(true);
    // get players tickets    
    $tickets = TicketsModel::instance()->getAllUnplayedTickets();
    if(!count($tickets)){
        messageLn("\nSorry, no tickets yet ;)");
        releaseLock();
        exit;
    }
    $lotteryCombination = array();
    messageLn(" [done]  -> " . number_format((microtime(true) - $time),3) . " s.");
    messageLn("      Tickets count - " . count($tickets));

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
            $forbiddenNums = array(); // 1
            while (count($combination) < $_ballsCount ) {
                $rand = mt_rand(1, $_variantsCount);

                if (!in_array($rand, $combination) && !in_array($rand, $forbiddenNums)) {
                    $combination[] = $rand;
                }
            }
            $lotteryCombinations[] = $combination;
        }
        message("   Sorting bets");
        $time = microtime(true);
        $bets = array();
        foreach ($tickets as $ticket)
        {
            foreach ((array)$ticket->getCombination() as $num)
            {

                if($num !== false)
                {
                    @$bets[$num]++;
                }
            }
        }
        $time = microtime(true);
        messageLn("    [done]  -> " . number_format((microtime(true) - $tgt),3) . " s.");


//        message("Count balls for every combinations");
//        $tgt = microtime(true);
//        $combBalls = array();
//        foreach ($tickets as $ticket)
//            foreach ($lotteryCombinations as $id => $combination)
//                @$combBalls[$id][count(array_intersect((array)$ticket->getCombination(),$combination))]++;
//         messageLn("    [done]  -> " . number_format((microtime(true) - $tgt),3) . " s.");



        // get most better combination
        $maxWin = 0;
        $lotteryCombination = array();
        $combinationsWeight = array();

        foreach ($lotteryCombinations as $id => $combination)
            foreach ($tickets as $ticket)
                if($compare=count(array_intersect((array)$ticket->getCombination(),$combination))) {
//                    if($compare>4) {
//                        message("   > 4balls");
//                        unset($combinationsWeight[$id]);
//                        continue 2;
//                    }

                    if ($gamePrizes['UA'][$compare]['currency'] == GameSettings::CURRENCY_MONEY)
                        @$combinationsWeight[$id] += $gamePrizes['UA'][$compare]['sum'];
                }

        foreach ($combinationsWeight as $id => $sum)
            $combinationsWeight[$id]=(int)$sum;

//        foreach ($lotteryCombinations as $id => $combination) {
//            $combinationWin = 0;
//            foreach ($combination as $combinationNum) {
//                foreach ($bets as $num => $bet) {
//                    if ($combinationNum == $num) {
//                        $combinationWin += $bet;
//                    }
//                }
//            }
//            $combinationsWeight[$id] = $combinationWin;
//            if ($combinationWin > $maxWin) {
//                $maxWin = $combinationWin;
//            }
//        }

        // late night magick ;O
        asort($combinationsWeight);
        $combinationsWeight = array_flip($combinationsWeight);
        print_r($combinationsWeight);
        $lotteryCombination = $lotteryCombinations[array_shift($combinationsWeight)];

    }
    messageLn("[done]  -> " . number_format((microtime(true) - $time),2) . " s.");

    message("Compare tickets");
    $time = microtime(true);
    $lastIterationReached = false;

    while (true) {
        $playersPlayed  = array();
        $pointsWonTotal = 0;
        $moneyWonTotal  = 0;
        $playersWinned = array();
        $combBalls = array();

        foreach ($tickets as $ticket) {
            $compares = 0;
            foreach ((array)$ticket->getCombination() as $ticketBet)
            {
                if($ticketBet !== false)
                {
                    foreach ($lotteryCombination as $lotteryBet)
                    {
                        if ($ticketBet == $lotteryBet)
                        {
                            $compares++;
                        }
                    }
                }
            }

            // compile players and tickets
            if (!isset($playersPlayed[$ticket->getPlayerId()])) {
                $playersPlayed[$ticket->getPlayerId()] = array(
                    'tickets'   => array(),
                );
            }
            $playersPlayed[$ticket->getPlayerId()]['tickets'][$ticket->getId()] = $compares;
            // ticket win
            if ($compares > 0) {

                @$combBalls[$compares]++;

                if (!isset($playersWinned[$ticket->getPlayerId()])) {
                    $playersWinned[$ticket->getPlayerId()] = $ticket->getPlayerId();
                }
                // calculate point or UA money total
                if ($gamePrizes['UA'][$compares]['currency'] == GameSettings::CURRENCY_MONEY) {
                    $moneyWonTotal += $gamePrizes['UA'][$compares]['sum'];                    
                } else {
                    $pointsWonTotal += $gamePrizes['UA'][$compares]['sum'];
                }
            }
        }


//        if (!$gameSettings->getJackpot()) {
//            if ($moneyWonTotal > $gameSettings->getTotalWinSum() && !$lastIterationReached) {
//                message(" limit -> ");
//
//                if (count($combinationsWeight)  == 1) {
//                    $lastIterationReached = true;
//                }
//                // restart with lower weight combination
//                $lotteryCombination = $lotteryCombinations[array_shift($combinationsWeight)];
//                continue;
//            } 
//        }

        break;
    }

    messageLn(" [done]  -> " . number_format((microtime(true) - $time),2) . " s.");
    message("Get players country setting");
    $time = microtime(true);

    $playersCountry = array();
    if($playersPlayed)
    {
        DB::Connect()->query(sprintf("SELECT `Id`, `Country` FROM `Players` WHERE `Id` IN (%s)", join(",", array_keys($playersPlayed))))->fetchAll(PDO::FETCH_FUNC, function($plid, $country) use (&$playersCountry) {
            $playersCountry[$plid] = $country;
            return $country;
        });
    }

    messageLn(" [done]  -> " . number_format((microtime(true) - $time),2) . " s.");
    messageLn("");

    message("Storing lottery data");
    $time = microtime(true);
    // create lottery instance;
    $lottery = new Lottery();
    $lottery->setCombination($lotteryCombination)
            ->setWinnersCount(count($playersWinned))
            ->setMoneyTotal($moneyWonTotal)
            ->setPointsTotal($pointsWonTotal)
            ->setBallsTotal($combBalls);
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
    $queries = array
    (
        'transactions'  => "INSERT INTO `Transactions` (`PlayerId`, `Currency`, `Sum`, `Description`, `Date`) VALUES %s",
        'players'       => "UPDATE `Players` SET `GamesPlayed`=`GamesPlayed`+1,`Money`=CASE %s END,`Points`=CASE %s END WHERE `Id` IN (%s)",
        'lotteryWins'   => "INSERT INTO `PlayerLotteryWins` (`LotteryId`, `PlayerId`, `Date`, `MoneyWin`, `PointsWin`) VALUES %s",
        'tickets'       => array(),
    );

    $transactionsSql    = $playersMoneySql = $playersPointsSql = $ticketsSumSql = $ticketsCurrencySql = $lotteryWinSql = array();
    $lid                = (int)$lottery->getId();

    foreach ($playersPlayed as $playerId => $data)
    {
        $playerPoints = $playerMoney = 0;
        foreach ($data['tickets'] as $ticketId => $ticketCompare)
        {
            // get player country

            $pcountry   = isset($playersCountry[$playerId])
                        ? $playersCountry[$playerId]
                        : null;

            if (!in_array($pcountry, Config::instance()->langs)) {
                $pcountry = Config::instance()->defaultLang;
            }

            $win = 0;
            $currency = GameSettings::CURRENCY_POINT;
            if ($ticketCompare > 0) {
                $win = $gamePrizes[$pcountry][$ticketCompare]['sum'];
                $currency = $gamePrizes[$pcountry][$ticketCompare]['currency'];    
            }

            if ($currency == GameSettings::CURRENCY_MONEY) {
                $playerMoney += $win; 
            } else {
                $playerPoints += $win;
            }

            $tid = (int)$ticketId;
            $win = DB::Connect()->quote($win);
            $cur = DB::Connect()->quote($currency);

            $queries['tickets'][]= "($tid,$win,$cur,$lid)";

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
            DB::Connect()->quote($lid),
            DB::Connect()->quote($playerId),
            DB::Connect()->quote(time()),
            DB::Connect()->quote($playerMoney),
            DB::Connect()->quote($playerPoints),
        ));
    }

    $queries['tickets']      = sprintf('INSERT IGNORE INTO `LotteryTickets` (`Id`, `TicketWin`, `TicketWinCurrency`, `LotteryId`) VALUES %s ON DUPLICATE KEY UPDATE `TicketWin` = VALUES(`TicketWin`), `TicketWinCurrency` = VALUES(`TicketWinCurrency`), `LotteryId` = VALUES(`LotteryId`)', implode(',', $queries['tickets']));
    $queries['transactions'] = sprintf($queries['transactions'], join(',', $transactionsSql));
    $queries['players']      = sprintf($queries['players'], join(" ", $playersMoneySql), join(" ", $playersPointsSql), join(",", array_keys($playersPlayed)));
    $queries['lotteryWins']  = sprintf($queries['lotteryWins'], join(',', $lotteryWinSql));
    messageLn(" [done]  -> " . number_format((microtime(true) - $time),2) . " s.");

    messageLn("Storing data");
    $time = microtime(true);
    try {
        foreach ($queries as $name=>$query) {
            message("   $name");
            $qtime = microtime(true);
            DB::Connect()->query($query);
            messageLn(" [done]  -> " . number_format((microtime(true) - $qtime),2) . " s.");
        }
    } catch (PDOException $e) {
        messageLn($e->getMessage());
    }
    messageLn("[done]  -> " . number_format((microtime(true) - $time),2) . " s.");

    $lottery->publish();

    messageLn("");
    echo "Players won count " . count($playersWinned) . PHP_EOL;

    echo "Money total " . $moneyWonTotal . PHP_EOL;
    echo "Points total " . $pointsWonTotal . PHP_EOL;
    echo "Balls compares: ";
    print_r($lotteryCombination );
    print_r($combBalls );
        echo PHP_EOL;

    releaseLock();
} else {
    messageLn("It is not time yet ;)");
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

function recompile(){


    message("Get settings");
    $time = microtime(true);
    $gameSettings = GameSettingsModel::instance()->loadSettings();
    $gamePrizes   = $gameSettings->getPrizes();
    messageLn(" [done]  -> " . number_format((microtime(true) - $time),3) . " s.");

    global $_ballsCount;    $_ballsCount    = 6;
    global $_variantsCount; $_variantsCount = 49;
    $gt = microtime(true);
    messageLn("Recompile lottery");
    $sql = "SELECT Id, Date FROM `Lotteries` ORDER BY Id DESC";
    $sth = DB::Connect()->prepare($sql);
    $sth->execute();
    $lotteriesData = $sth->fetchAll();

    foreach ($lotteriesData as $lotteryData)
    {
        $lid=$lotteryData['Id'];
        message("   Get tickets №$lid");
        $time = microtime(true);
        // get players tickets
        $tickets = TicketsModel::instance()->getAllUnplayedTickets($lid);
        $lotteryCombination = array();
        messageLn(" [done]  -> " . number_format((microtime(true) - $time),3) . " s.");
        messageLn("      Tickets count - " . count($tickets));

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
                $forbiddenNums = array(); // 1
                while (count($combination) < $_ballsCount ) {
                    $rand = mt_rand(1, $_variantsCount);

                    if (!in_array($rand, $combination) && !in_array($rand, $forbiddenNums)) {
                        $combination[] = $rand;
                    }
                }
                $lotteryCombinations[] = $combination;
            }
            message("   Sorting bets");
            $time = microtime(true);
            $bets = array();
            foreach ($tickets as $ticket)
            {
                foreach ((array)$ticket->getCombination() as $num)
                {

                    if($num !== false)
                    {
                        @$bets[$num]++;
                    }
                }
            }
            $time = microtime(true);
            messageLn("    [done]  -> " . number_format((microtime(true) - $tgt),3) . " s.");


            // get most better combination
            $maxWin = 0;
            $lotteryCombination = array();
            $combinationsWeight = array();

            foreach ($lotteryCombinations as $id => $combination)
                foreach ($tickets as $ticket)
                    if($compare=count(array_intersect((array)$ticket->getCombination(),$combination))) {
                        if ($gamePrizes['UA'][$compare]['currency'] == GameSettings::CURRENCY_MONEY)
                            @$combinationsWeight[$id] += $gamePrizes['UA'][$compare]['sum'];
                    }

            foreach ($combinationsWeight as $id => $sum)
                $combinationsWeight[$id]=(int)$sum;

            // late night magick ;O
            asort($combinationsWeight);
            $combinationsWeight = array_flip($combinationsWeight);
            print_r($combinationsWeight);
            $lotteryCombination = $lotteryCombinations[array_shift($combinationsWeight)];

        }
        messageLn("[done]  -> " . number_format((microtime(true) - $time),2) . " s.");

        message("Compare tickets");
        $time = microtime(true);
        $lastIterationReached = false;

        while (true) {
            $playersPlayed  = array();
            $pointsWonTotal = 0;
            $moneyWonTotal  = 0;
            $playersWinned = array();
            $combBalls = array();

            foreach ($tickets as $ticket) {
                $compares = 0;
                foreach ((array)$ticket->getCombination() as $ticketBet)
                {
                    if($ticketBet !== false)
                    {
                        foreach ($lotteryCombination as $lotteryBet)
                        {
                            if ($ticketBet == $lotteryBet)
                            {
                                $compares++;
                            }
                        }
                    }
                }

                // compile players and tickets
                if (!isset($playersPlayed[$ticket->getPlayerId()])) {
                    $playersPlayed[$ticket->getPlayerId()] = array(
                        'tickets'   => array(),
                    );
                }
                $playersPlayed[$ticket->getPlayerId()]['tickets'][$ticket->getId()] = $compares;
                // ticket win
                if ($compares > 0) {

                    @$combBalls[$compares]++;

                    if (!isset($playersWinned[$ticket->getPlayerId()])) {
                        $playersWinned[$ticket->getPlayerId()] = $ticket->getPlayerId();
                    }
                    // calculate point or UA money total
                    if ($gamePrizes['UA'][$compares]['currency'] == GameSettings::CURRENCY_MONEY) {
                        $moneyWonTotal += $gamePrizes['UA'][$compares]['sum'];
                    } else {
                        $pointsWonTotal += $gamePrizes['UA'][$compares]['sum'];
                    }
                }
            }

            break;
        }

        messageLn(" [done]  -> " . number_format((microtime(true) - $time),2) . " s.");
        message("Get players country setting");
        $time = microtime(true);

        $playersCountry = array();
        if($playersPlayed)
        {
            DB::Connect()->query(sprintf("SELECT `Id`, `Country` FROM `Players` WHERE `Id` IN (%s)", join(",", array_keys($playersPlayed))))->fetchAll(PDO::FETCH_FUNC, function($plid, $country) use (&$playersCountry) {
                $playersCountry[$plid] = $country;
                return $country;
            });
        }

        messageLn(" [done]  -> " . number_format((microtime(true) - $time),2) . " s.");
        messageLn("");

        message("Storing lottery data");
        $time = microtime(true);
        // create lottery instance;
        $lottery = new Lottery();
        $lottery->setId($lid)
            ->setDate($lotteryData['Date'])
            ->setCombination($lotteryCombination)
            ->setWinnersCount(count($playersWinned))
            ->setMoneyTotal($moneyWonTotal)
            ->setPointsTotal($pointsWonTotal)
            ->setBallsTotal($combBalls);
        try {
            $lottery->update();
        } catch (EntityException $e) {
            echo "Something gone wrong and i cant store lottery data" . PHP_EOL;
            echo $e->getMessage() . PHP_EOL;
            exit;
        }
        messageLn(" [done]  -> " . number_format((microtime(true) - $time),2) . " s.");

        message("Calculations and data preparing");
        $time = microtime(true);
        $queries = array
        (
            'transactions'  => "INSERT INTO `Transactions` (`PlayerId`, `Currency`, `Sum`, `Description`, `Date`) VALUES %s",
            'players'       => "UPDATE `Players` SET `GamesPlayed`=`GamesPlayed`+1,`Money`=CASE %s END,`Points`=CASE %s END WHERE `Id` IN (%s)",
            'tickets'       => array(),
            'lotteryWins'   => "REPLACE INTO `PlayerLotteryWins` (`LotteryId`, `PlayerId`, `Date`, `MoneyWin`, `PointsWin`) VALUES %s",
        );

        $transactionsSql    = $playersMoneySql = $playersPointsSql = $ticketsSumSql = $ticketsCurrencySql = $lotteryWinSql = array();
        $lid                = (int)$lottery->getId();

        foreach ($playersPlayed as $playerId => $data)
        {
            $playerPoints = $playerMoney = 0;
            foreach ($data['tickets'] as $ticketId => $ticketCompare)
            {
                // get player country

                $pcountry   = isset($playersCountry[$playerId])
                    ? $playersCountry[$playerId]
                    : null;

                if (!in_array($pcountry, Config::instance()->langs)) {
                    $pcountry = Config::instance()->defaultLang;
                }

                $win = 0;
                $currency = GameSettings::CURRENCY_POINT;
                if ($ticketCompare > 0) {
                    $win = $gamePrizes[$pcountry][$ticketCompare]['sum'];
                    $currency = $gamePrizes[$pcountry][$ticketCompare]['currency'];
                }

                if ($currency == GameSettings::CURRENCY_MONEY) {
                    $playerMoney += $win;
                } else {
                    $playerPoints += $win;
                }

                $tid = (int)$ticketId;
                $win = DB::Connect()->quote($win);
                $cur = DB::Connect()->quote($currency);

                $queries['tickets'][]= "($tid,$win,$cur,$lid)";

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
                DB::Connect()->quote($lid),
                DB::Connect()->quote($playerId),
                DB::Connect()->quote(time()),
                DB::Connect()->quote($playerMoney),
                DB::Connect()->quote($playerPoints),
            ));
        }

        $queries['tickets']      = sprintf('INSERT IGNORE INTO `LotteryTickets` (`Id`, `TicketWin`, `TicketWinCurrency`, `LotteryId`) VALUES %s ON DUPLICATE KEY UPDATE `TicketWin` = VALUES(`TicketWin`), `TicketWinCurrency` = VALUES(`TicketWinCurrency`), `LotteryId` = VALUES(`LotteryId`)', implode(',', $queries['tickets']));
        $queries['transactions'] = sprintf($queries['transactions'], join(',', $transactionsSql));
        $queries['players']      = sprintf($queries['players'], join(" ", $playersMoneySql), join(" ", $playersPointsSql), join(",", array_keys($playersPlayed)));
        $queries['lotteryWins']  = sprintf($queries['lotteryWins'], join(',', $lotteryWinSql));
        messageLn(" [done]  -> " . number_format((microtime(true) - $time),2) . " s.");

        messageLn("Storing data");
        $time = microtime(true);
        try {
            foreach ($queries as $name=>$query) {
                message("   $name");
                $qtime = microtime(true);
                DB::Connect()->query($query);
                messageLn(" [done]  -> " . number_format((microtime(true) - $qtime),2) . " s.");
            }
        } catch (PDOException $e) {
            messageLn($e->getMessage());
        }
        messageLn("[done]  -> " . number_format((microtime(true) - $time),2) . " s.");

        $lottery->publish();

        messageLn("");
        echo "Players won count " . count($playersWinned) . PHP_EOL;

        echo "Money total " . $moneyWonTotal . PHP_EOL;
        echo "Points total " . $pointsWonTotal . PHP_EOL;
        echo "Balls compares: ";
        print_r($lotteryCombination );
        print_r($combBalls );
        echo PHP_EOL;
    }

    messageLn("");
    messageLn("Total time s  -> " . number_format((microtime(true) - $gt),2) . " s.");
    exit;
}

// */
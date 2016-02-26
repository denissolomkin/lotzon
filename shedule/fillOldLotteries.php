<?php
/*
require_once('init.php');

$sql = "SELECT * FROM `Lotteries` WHERE `Ready` = 1 ORDER BY `Id`";
$sth = DB::Connect()->prepare($sql);
$sth->execute();
$lotteries = $sth->fetchAll();

foreach ($lotteries as $lottery) {
    $lotteryId          = $lottery['Id'];
    $lotteryCombination = unserialize($lottery['Combination']);

    // Players total
    $sql = "SELECT
                    count(DISTINCT PlayerId) as c
                FROM
                  `LotteryTicketsArchive`
                WHERE
                    `LotteryId` = :lotteryid";
    $sth = DB::Connect()->prepare($sql);
    $sth->execute(array(
        ':lotteryid' => $lotteryId,
    ));
    $PlayersCount     = $sth->fetch()['c'];

    // Winners total
    $sql = "SELECT
                    count(DISTINCT PlayerId) as c
                FROM
                  `LotteryTicketsArchive`
                WHERE
                    `LotteryId` = :lotteryid
                AND
                    `TicketWin` > 0";
    $sth = DB::Connect()->prepare($sql);
    $sth->execute(array(
        ':lotteryid' => $lotteryId,
    ));
    $WinnersCount     = $sth->fetch()['c'];

    // Balls total
    $BallsTotal = array(
        1 => 0,
        2 => 0,
        3 => 0,
        4 => 0,
        5 => 0,
        6 => 0,
    );
    $sql = "SELECT
                    *
                FROM
                  `LotteryTicketsArchive`
                WHERE
                    `LotteryId` = :lotteryid
                AND
                    `TicketWin` > 0
                ORDER BY `Id`";
    $sth = DB::Connect()->prepare($sql);
    $sth->execute(array(
        ':lotteryid' => $lottery['Id'],
    ));
    foreach ($sth->fetchAll() as $ticket) {
        $ballsCount = count(array_intersect($lotteryCombination, unserialize($ticket['Combination'])));
        $BallsTotal[$ballsCount] ++;
    }

    // LastTicketId
    $sql = "SELECT
                    Id
                FROM
                  `LotteryTicketsArchive`
                WHERE
                    `LotteryId` = :lotteryid
                ORDER BY Id DESC
                LIMIT 1";
    $sth = DB::Connect()->prepare($sql);
    $sth->execute(array(
        ':lotteryid' => $lotteryId,
    ));
    $LastTicketId     = $sth->fetch()['Id'];

    // Increment
    if ($lotteryId > 84) {
        $PlayersCountIncr = 1810; // 1750/0.967
        $WinnersCountIncr = 1750;
        $BallsTotalIncr = array(
            1 => 200,
            2 => 120,
            3 => 30,
            4 => 2,
            5 => 0,
            6 => 0,
        );
    } elseIf ($lotteryId > 76) {
        $PlayersCountIncr = 1035; // 1750/0.967
        $WinnersCountIncr = 1000;
        $BallsTotalIncr = array(
            1 => 90,
            2 => 40,
            3 => 10,
            4 => 1,
            5 => 0,
            6 => 0,
        );
    } else {
        $PlayersCountIncr = 0;
        $WinnersCountIncr = 0;
        $BallsTotalIncr = array(
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
        );
    }

    $Prizes     = LotterySettingsModel::instance()->loadSettings()->getPrizes();
    $PrizesGold = LotterySettingsModel::instance()->loadSettings()->getGoldPrizes();

    // Update lottery
    $sql = "UPDATE `Lotteries` SET `LastTicketId` = :LastTicketId,
                                   `PlayersCount` = :PlayersCount,
                                   `PlayersCountIncr` = :PlayersCountIncr,
                                   `WinnersCount` = :WinnersCount,
                                   `WinnersCountIncr` = :WinnersCountIncr,
                                   `BallsTotal` = :BallsTotal,
                                   `BallsTotalIncr` = :BallsTotalIncr,
                                   `Prizes` = :Prizes,
                                   `PrizesGold` = :PrizesGold WHERE `Id` = :id";

    $sth = DB::Connect()->prepare($sql)->execute(array(
        ':id'               => $lotteryId,
        ':LastTicketId'     => $LastTicketId,
        ':PlayersCount'     => $PlayersCount,
        ':PlayersCountIncr' => $PlayersCountIncr,
        ':WinnersCount'     => $WinnersCount,
        ':WinnersCountIncr' => $WinnersCountIncr,
        ':BallsTotal'       => serialize($BallsTotal),
        ':BallsTotalIncr'   => serialize($BallsTotalIncr),
        ':Prizes'           => serialize($Prizes),
        ':PrizesGold'       => serialize($PrizesGold),
    ));

}

\LotteriesModel::instance()->recache();

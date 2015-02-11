<?php

/*
if(!file_exists($tmp = 'lottery.fix.night.95.tmp'))
{
	echo $tmp.PHP_EOL;

	file_put_contents($tmp, '');

	require_once('lottery.inc.php');

	$lid = 95;

	$SQL = "UPDATE
				LotteryTickets  lt
			SET
				lt.TicketWin = 0,
				lt.TicketWinCurrency = ''
			WHERE
					lt.LotteryId = $lid
				AND lt.B6	IS NULL
				AND lt.B16	IS NULL
				AND lt.B26	IS NULL
				AND lt.B31	IS NULL
				AND lt.B37	IS NULL
				AND lt.B43	IS NULL";

	DB::Connect()->query($SQL);

	$SQL = "UPDATE
							LotteryTickets	lt
				INNER JOIN	Players			p	ON	lt.PlayerId = p.Id
			SET
				p.GamesPlayed = p.GamesPlayed + 1
			WHERE
				lt.LotteryId >= $lid";

	DB::Connect()->query($SQL);
}

if(!file_exists($tmp = 'lottery.fix.95.tmp'))
{
	echo $tmp.PHP_EOL;

	file_put_contents($tmp, '');

	require_once('lottery.inc.php');

	$lid = 95;

	$SQL = "UPDATE
							LotteryTickets	lt
				INNER JOIN	LotterySettings	ls
				ON			IFNULL(B6, 0) + IFNULL(B16, 0) + IFNULL(B26, 0) + IFNULL(B31, 0) + IFNULL(B37, 0) + IFNULL(B43, 0) = ls.BallsCount
				INNER JOIN	Players p
				ON			lt.PlayerId = p.Id
				AND			CASE p.Country WHEN 'BY' THEN 'BY' WHEN 'UA' THEN 'UA' ELSE 'RU' END = ls.CountryCode
			SET
    			p.Points = p.Points - IF(ls.Currency = 'POINT', ls.Prize, 0),
    			p.Money = p.Money - IF(ls.Currency = 'POINT', 0, ls.Prize)

			WHERE
				lt.LotteryId = $lid
				AND (  B6  IS NOT NULL
					OR B16 IS NOT NULL
					OR B26 IS NOT NULL
					OR B31 IS NOT NULL
					OR B37 IS NOT NULL
					OR B43 IS NOT NULL)";
	DB::Connect()->query($SQL);

	$SQL = "DELETE FROM
				Transactions
			WHERE
					`Date`			= 1423076403
				AND	`Description`	= 'Выигрыш в розыгрыше'";
	DB::Connect()->query($SQL);

	PlayerTotal($lid);
	Transactions($lid);
}
// */

if($_SERVER['argv'][1] == 'dbg')
{
/*
	require_once('lottery.inc.php');

	$comb = Array
	(
		'combination' => Array
		(
			'0' => 'B6',
			'1' => 'B16',
			'2' => 'B26',
			'3' => 'B31',
			'4' => 'B37',
			'5' => 'B43',
		),
		'id' => $lid,
	);

	$comb['fields'] = $comb['combination'];

	ApplyLotteryCombination($comb);

//	ConverDB();
//	RestoreAllTickets();
/*
	$rounds     = 50;
	$ballsRange = 40;

	HoldLottery(0, 0, $ballsRange, $rounds);
	sleep(20);

	LotterySimulation('simulation.html', 0, $ballsRange, $rounds);
// */
}
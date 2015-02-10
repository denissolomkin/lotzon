<?php

require_once('init.php');

ini_set('memory_limit', -1);

global $_ballsCount;    $_ballsCount    = 6;
global $_variantsCount; $_variantsCount = 49;
global $gameSettings;   $gameSettings   = GameSettingsModel::instance()->loadSettings();


function timeToRunLottery()
{
	global $gameSettings;

	if(@$_SERVER['argv'][1] == 'dbg')
	{
		return true;
	}

	$currentTime = strtotime(date('H:i'), 0);

	foreach($gameSettings->getGameTimes() as $time)
	{
		if($currentTime == $time)
		{
			return true;
		}
	}

	return false;
}
function PlayerLotteryWins($lid)
{
	$time = microtime(true);
	echo 'PlayerLotteryWins: ';

	$SQL = "INSERT INTO PlayerLotteryWins
			(
				PlayerId,
				MoneyWin,
				PointsWin,
				Date,
				LotteryId
			)
			SELECT
				lt.PlayerId,
				SUM(IF(lt.TicketWinCurrency = 'POINT', 0, lt.TicketWin)) AS MoneyWin,
				SUM(IF(lt.TicketWinCurrency = 'POINT', lt.TicketWin, 0)) AS PointsWin,
				l.Date,
				l.Id
			FROM
			 				Lotteries		l
				INNER JOIN	LotteryTickets	lt	ON	l.Id = lt.LotteryId
			WHERE
					l.Id			= $lid
				AND lt.TicketWin	> 0
			GROUP BY
				lt.PlayerId";

	DB::Connect()->query($SQL);

	echo (microtime(true) - $time).PHP_EOL;
}
function Transactions($lid)
{
	$time = microtime(true);
	echo 'Transactions: ';

	$SQL = "INSERT INTO Transactions
			(
				PlayerId,
				Currency,
				Sum,
				Date,
				Balance,
				Description
			)
			SELECT
				lt.PlayerId,
				lt.TicketWinCurrency,
				SUM(lt.TicketWin),
				l.Date,
				IF(lt.TicketWinCurrency = 'POINT', p.Points, p.Money),
				'Выигрыш в розыгрыше'
			FROM
							Lotteries		l
				INNER JOIN	LotteryTickets	lt	ON	l.Id = lt.LotteryId
				INNER JOIN	Players			p	ON	lt.PlayerId = p.Id
			WHERE
					l.Id 			= $lid
				AND lt.TicketWin	> 0
			GROUP BY
				lt.PlayerId,
				lt.TicketWinCurrency";

	DB::Connect()->query($SQL);

	echo (microtime(true) - $time).PHP_EOL;
}
function ApplyLotteryTickets($comb)
{
	echo 'ApplyLotteryCombination'.PHP_EOL;


	$time = microtime(true);
	echo '  Update win tickets: ';

	$defaultCountry  = Config::instance()->defaultLang;
	$select = $where = array();

	$SQL = "SELECT
				CountryCode AS code
			FROM
				LotterySettings
			WHERE
				CountryCode <> '$defaultCountry'
			GROUP BY
				CountryCode";
	$codes = DB::Connect()->query($SQL)->fetchAll(PDO::FETCH_FUNC, function($code)
	{
		return "WHEN '$code' THEN '$code'";
	});

	foreach($comb['fields'] as $field)
	{
		$select[]= "IFNULL($field, 0)";
		$where []= "$field IS NOT NULL";
	}

	$lid = (int)$comb['id'];

	$SQL = "UPDATE
							LotteryTickets	lt
				INNER JOIN	LotterySettings	ls	ON	%s = ls.BallsCount
				INNER JOIN	Players			p	ON	lt.PlayerId = p.Id
												AND CASE p.Country %s ELSE '$defaultCountry' END = ls.CountryCode
			SET
				lt.TicketWin		 = ls.Prize,
  				lt.TicketWinCurrency = ls.Currency,
  				lt.LotteryId		 = $lid

			WHERE
				lt.LotteryId = 0
				AND (%s)";

	$SQL = sprintf($SQL, implode('+', $select), implode(' ', $codes), implode(' OR ', $where));

	DB::Connect()->query($SQL);

	echo (microtime(true) - $time).PHP_EOL;



	$time = microtime(true);
	echo '  Update losing tickets: ';

	DB::Connect()->query("UPDATE LotteryTickets SET LotteryId	= $lid  WHERE LotteryId = 0");

	echo (microtime(true) - $time).PHP_EOL;
}
function PlayerTotal($lid)
{
	$time = microtime(true);
	echo 'PlayerTotal: ';

	$SQL = "UPDATE
							Players				p
				INNER JOIN	PlayerLotteryWins	plw	 ON  plw.PlayerId = p.Id
			SET
				p.Points = p.Points + plw.PointsWin,
				p.Money  = p.Money  + plw.MoneyWin

			WHERE
				plw.LotteryId = $lid";
	DB::Connect()->query($SQL);

	echo (microtime(true) - $time).PHP_EOL;
}
function PlayerCounter($lid)
{
	$time = microtime(true);
	echo 'PlayerCounter: ';

	$SQL = "UPDATE
							LotteryTickets	lt
				INNER JOIN	Players			p	ON	lt.PlayerId = p.Id
			SET
				p.GamesPlayed = p.GamesPlayed + 1
			WHERE
				lt.LotteryId = $lid";

	DB::Connect()->query($SQL);

	echo (microtime(true) - $time).PHP_EOL;
}
function ApplyLotteryCombination(&$comb)
{
	if(!$comb)
	{
		return;
	}

	$lid = (int)$comb['id'];

	ApplyLotteryTickets($comb);
	PlayerLotteryWins($lid);
	PlayerTotal($lid);
	Transactions($lid);
	PlayerCounter($lid);

	DB::Connect()->query("UPDATE Lotteries SET Ready = 1 WHERE Id = $lid");

	unset($comb['fields']);
}
function SetLotteryCombination($comb)
{
	if(!$comb)
	{
		return;
	}

	global $_ballsCount;

	$time = microtime(true);
	echo 'SetLotteryCombination: ';

	$Combination = $where = array();

	$comb['fields'] = $comb['combination'];

	foreach($comb['fields'] as &$ball)
	{
		$Combination[]= (int)substr($ball, 1);
		$ball = '`'.addslashes($ball).'`';
		$where[]= "$ball IS NOT NULL";
	}

	shuffle($Combination);
	$Combination = serialize($Combination);


	$ballsArray = array_flip(range(1, $_ballsCount - 1));
	$ballsArray = array_intersect_key($comb, $ballsArray);
	$ballsArray = serialize($ballsArray);


	$SQL = 'SELECT
				COUNT(DISTINCT(PlayerId))
			FROM
				LotteryTickets
			WHERE
				LotteryId = 0
				AND ('.implode(' OR ', $where).')';
	$WinnersCount = current(DB::Connect()->query($SQL)->fetch());


	$SQL = "INSERT INTO Lotteries
				(`Date`, Combination, WinnersCount, MoneyTotal, PointsTotal, BallsTotal, %s)
			VALUES
				(%d, '%s', %d, %f, %d, '%s', 1, 1, 1, 1, 1, 1)";

	$SQL = sprintf($SQL,	implode(',', $comb['fields']),
							time(),
							$Combination,
							$WinnersCount,
							$comb['MoneyTotal'],
							$comb['PointsTotal'],
							$ballsArray);

	DB::Connect()->query($SQL);

	$comb['id']           = DB::Connect()->lastInsertId();
	$comb['WinnersCount'] = $WinnersCount;

	echo (microtime(true) - $time).PHP_EOL;

	return $comb;
}
function GetLotteryCombinationStatistics()
{
	global $_variantsCount;

	$time = microtime(true);
	echo 'GetLotteryCombinationStatistics: ';

	$fields = array();

	for($i = 1; $i <= $_variantsCount; $i++)
	{
		$fields[]= "COUNT(B$i) as B$i";
	}

	$SQL    = sprintf('SELECT %s FROM LotteryTickets WHERE LotteryId = 0', implode(',', $fields));
	$stats  = DB::Connect()->query($SQL)->fetch();

	asort($stats);

	echo (microtime(true) - $time).PHP_EOL.PHP_EOL;

	$echo = array();
	foreach($stats as $ball => $count)
	{
		$echo[]= str_pad($ball, 3, '_', STR_PAD_RIGHT).":$count";
	}
	$echo = implode(', ', $echo);
	echo wordwrap($echo);

	echo PHP_EOL.PHP_EOL.PHP_EOL;

	return $stats;
}
function GetLotteryCombination($ballsStart, $ballsRange, $rounds, $return, $orderBy)
{
	global $_ballsCount;
	global $_variantsCount;

	static $cache = array();

	$ballsStart = min($ballsStart, $_variantsCount - $_ballsCount);
	$ballsRange+= $_ballsCount;
	$ballsRange = min($ballsRange, $_variantsCount - $ballsStart);

	$stats = GetLotteryCombinationStatistics();

	if(!array_sum($stats))
	{
		return;
	}

	$time = microtime(true);
	echo 'GetLotteryCombination:'.PHP_EOL;

	$ballsStatSQL = array();
	for($i = 1; $i < $_ballsCount; $i++)
	{
		$ballsStatSQL[]= "SUM(IF(stat.BallsCount = $i, stat.cnt, 0)) AS '$i'";
	}

	$stats = array_splice($stats, $ballsStart, $ballsRange);

	for($r = $rounds, $rountdsStats = array(); $r--;)
	{
		$balls  = array_rand($stats, $_ballsCount);     asort($balls);  $balls = array_values($balls);
		$hash   = serialize($balls);

		if(isset($cache[$hash]))
		{
			continue;
		}

		$t = microtime(true);

		$fields = array_map(function($ball)
		{
			return "IFNULL($ball, 0)";
		},
		$balls);

		$SQL = "SELECT
                    SUM(IF(Currency = 'POINT', Prize / 100, Prize) * cnt)	AS UAH,
                    SUM(IF(Currency = 'POINT', Prize, 0) * cnt)				AS PointsTotal,
                    SUM(IF(Currency = 'POINT', 0, Prize) * cnt)				AS MoneyTotal,
                    SUM(cnt)												AS TicketsCount,
                    MAX(stat.BallsCount)									AS BallsMax,
                    %s
                FROM
                (   SELECT
                        COUNT(*) AS cnt,
                        sm.BallsCount
                    FROM
                    (   SELECT
                            %s AS BallsCount
                        FROM
                            LotteryTickets
                        WHERE
                            LotteryId = 0
                    )	sm

                    WHERE
                        sm.BallsCount <> 0
                    GROUP BY
                        sm.BallsCount
                )
                stat  INNER JOIN LotterySettings ls USING(BallsCount)

                WHERE
                    ls.CountryCode = 'UA'";

		$SQL = sprintf($SQL, implode(',', $ballsStatSQL), implode('+', $fields));

		$rountdStats = DB::Connect()->query($SQL)->fetch();
		$rountdStats['combination'] = $balls;

		$rountdsStats[(int)$rountdStats[$orderBy]] = $rountdStats;

		echo '  check candidate [ '.implode(', ', $balls).' ] ('.number_format($rountdStats[$orderBy]).'): '.(microtime(true) - $t).PHP_EOL;

		$cache[$hash]= true;
	}

	ksort($rountdsStats);   $rountdsStats = array_values($rountdsStats);

	$return = min($return, count($rountdsStats)-1);

	echo 'Total: '.(microtime(true) - $time).PHP_EOL;

	return $rountdsStats[$return];
}
function ResetLottery($lid = null)
{
	if($lid)
	{
		$SQL = "UPDATE
					LotteryTickets l
				SET
					l.LotteryId = 0
				WHERE
					l.LotteryId = $lid";
	}
	elseif(!isset($lid))
	{
		$SQL = "UPDATE
				(
					SELECT
    					MAX(LotteryTickets.LotteryId) AS mx
					FROM
					 	LotteryTickets
				)	mx

  				INNER JOIN LotteryTickets l	 ON	 mx.mx = l.LotteryId
				SET
  					l.LotteryId = 0";
	}

	if(isset($SQL))
	{
		$time = microtime(true);

		echo 'ResetLottery'.($lid ? " $lid" : null).': ';

		DB::Connect()->query($SQL);

		echo (microtime(true) - $time).PHP_EOL;
	}
}
function HoldLottery($lid = 0, $ballsStart = 0, $ballsRange = 3, $rounds = 250, $return = 0, $orderBy = 'MoneyTotal')
{
	$time = microtime(true);

			ConverDB();
			ResetLottery($lid);
	$comb = GetLotteryCombination($ballsStart, $ballsRange, $rounds, $return, $orderBy);
	$comb = SetLotteryCombination($comb);
			ApplyLotteryCombination($comb);


	echo PHP_EOL.PHP_EOL;   print_r($comb);

//DB::Connect()->beginTransaction();
//DB::Connect()->commit();
//DB::Connect()->rollBack();

	echo PHP_EOL.'Total time: '.(microtime(true) - $time).PHP_EOL.PHP_EOL.PHP_EOL.'==============================================='.PHP_EOL.PHP_EOL.PHP_EOL;

	return $comb;
}
function LotterySimulation($output = 'simulation.html', $ballsStart = 0, $ballsRange = 3, $rounds = 250, $return = 0, $orderBy = 'MoneyTotal')
{
	$SQL = "SELECT
				LotteryId AS lid
			FROM
				LotteryTickets
			WHERE
				LotteryId BETWEEN 72 AND 79
			GROUP BY
				LotteryId
			ORDER BY
				LotteryId DESC";

	$results = DB::Connect()->query($SQL)->fetchAll(PDO::FETCH_FUNC, function($lid) use ($ballsStart, $ballsRange, $rounds, $return, $orderBy)
	{
		$row = HoldLottery($lid, $ballsStart, $ballsRange, $rounds, $return, $orderBy);

		sleep(60);

		if(isset($row))
		{
			$row = "<td><b>$lid:</b></td><td>".implode('</td><td>', $row['combination'])."</td><td><b>{$row['MoneyTotal']}</b></td>";
		}

		return $row;
	});

	$results = '<table border=1 cellpadding=5><tr>'.implode('</tr><tr>', $results).'</tr></table>';

	file_put_contents($output, $results);

}
function RestoreAllTickets()
{
	$SQL = "SELECT
			LotteryId AS lid,
			count(*) AS cnt
		FROM
			LotteryTickets
		GROUP BY
			LotteryId
		ORDER BY
			LotteryId DESC";

	DB::Connect()->query($SQL)->fetchAll(PDO::FETCH_FUNC, function($lid, $cnt)
	{

		$time = microtime(true);

		echo "Lottery #$lid ($cnt): ";

		RestoreTickets($lid);

		echo (microtime(true) - $time).PHP_EOL;

	});
}

function RestoreTickets($lid = 0)
{
	global $_variantsCount;

	$tckts = DB::Connect()->query("SELECT count(*) FROM LotteryTickets WHERE LotteryId = $lid")->fetch();
	$tckts = current($tckts);

	for($inc = 1000, $l1 = 0, $l2 = $inc; $l1 < $tckts; $l1 += $inc, $l2 += $inc)
	{
		$limit = "$l1, $l2";

		$SQL = 'INSERT IGNORE INTO LotteryTickets (Id';

		for ($i = 1; $i <= $_variantsCount; $i++)
		{
			$SQL .= ",B$i";
		}

		$SQL.= ') VALUES %s ON DUPLICATE KEY UPDATE ';

		for ($i = 1; $i <= $_variantsCount; $i++)
		{
			$SQL .= "B$i=VALUES(B$i),";
		}
		$SQL = substr($SQL, 0, -1);
		$cls = array();

		$lot = DB::Connect()->query("SELECT Id, Combination FROM LotteryTickets WHERE LotteryId = $lid LIMIT $limit")->fetchAll();
		foreach ($lot as $l)
		{
			if ($l['Combination'])
			{
				$balls = unserialize($l['Combination']);

				$vars = array();

				for ($i = 1; $i <= $_variantsCount; $i++)
				{
					$vars[] = in_array($i, $balls)
						? 1
						: 'NULL';
				}

				$cls[] = sprintf("({$l['Id']},%s)", implode(',', $vars));
			}
		}

		if($cls)
		{
			$SQL = sprintf($SQL, implode(',', $cls));
			DB::Connect()->query($SQL);
		}
	}
}
function ConverDB()
{
/*
	$time = microtime(true);

	echo PHP_EOL.'ConverDB: ';

	global $_variantsCount;

	if(!DB::Connect()->query('SHOW COLUMNS FROM Players LIKE "InviterId"')->fetch())
	{
		DB::Connect()->query('ALTER TABLE `Players` ADD `InviterId` INT(11) UNSIGNED	NOT NULL DEFAULT "0", ADD INDEX (`InviterId`)');
		DB::Connect()->query('ALTER TABLE `Players` ADD `Agent`		VARCHAR(255)		NOT NULL');
		DB::Connect()->query('CREATE TABLE IF NOT EXISTS `PlayerIps` (
								`PlayerId` int(11) unsigned NOT NULL,
								`Ip` int(11) unsigned NOT NULL,
								`Time` int(11) unsigned NOT NULL
							) ENGINE=InnoDB DEFAULT CHARSET=latin1');
		DB::Connect()->query('ALTER TABLE `PlayerIps` ADD PRIMARY KEY (`PlayerId`,`Ip`), ADD KEY `PlayerId` (`PlayerId`), ADD KEY `Ip` (`Ip`)');

	}

	if(!DB::Connect()->query('SHOW COLUMNS FROM Lotteries LIKE "BallsTotal"')->fetch())
	{
		DB::Connect()->query('ALTER TABLE Players			ADD INDEX `Country`		(`Country`)');
		DB::Connect()->query('ALTER TABLE LotterySettings	ADD INDEX `CountryCode`	(`CountryCode`)');
		DB::Connect()->query('ALTER TABLE LotterySettings	ADD INDEX `BallsCount`	(`BallsCount`)');
	}

	if(!DB::Connect()->query('SHOW COLUMNS FROM Lotteries LIKE "BallsTotal"')->fetch())
	{
		DB::Connect()->query('ALTER TABLE Lotteries ADD BallsTotal varchar(255) NULL');
	}

	if(!DB::Connect()->query('SHOW COLUMNS FROM Lotteries LIKE "BallsTotal"')->fetch())
	{
		DB::Connect()->query('ALTER TABLE Lotteries ADD BallsTotal varchar(255) NULL');
	}

	if(!DB::Connect()->query('SHOW COLUMNS FROM LotteryTickets LIKE "B1"')->fetch())
	{
//		foreach(array('Players', 'Lotteries', 'LotterySettings', 'LotteryTickets', 'PlayerLotteryWins', 'Transactions') as $table)
//		{
//			DB::Connect()->query("ALTER TABLE $table ENGINE='MyISAM'");
//		}

		$SQL_LT = 'ALTER TABLE LotteryTickets ';
		$SQL_L  = 'ALTER TABLE Lotteries ';

		for($i = 1; $i <= $_variantsCount; $i++)
		{
			$SQL_LT.= "ADD B$i TINYINT(1) NULL, ADD INDEX L$i (LotteryId,B$i),";
			$SQL_L .= "ADD B$i TINYINT(1) NULL, ADD INDEX L$i (B$i),";
		}

		$SQL_LT = substr($SQL_LT, 0, -1);
		$SQL_L  = substr($SQL_L,  0, -1);


		DB::Connect()->query($SQL_LT);
		DB::Connect()->query($SQL_L);

		DB::Connect()->query('ALTER TABLE `LotteryTickets` CHANGE `Combination` `Combination` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;');

		RestoreTickets();

		echo (microtime(true) - $time).PHP_EOL;

		return true;
	}

	echo (microtime(true) - $time).PHP_EOL;
// */
	return false;
}

?>
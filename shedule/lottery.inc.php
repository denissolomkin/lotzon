<?php

require_once('init.php');

ini_set('memory_limit', -1);

global $_ballsCount;    $_ballsCount    = 6;
global $_variantsCount; $_variantsCount = 49;
global $gameSettings;   $gameSettings   = LotterySettingsModel::instance()->loadSettings();

function timeToRunLottery()
{
	global $gameSettings;

	if(@$_SERVER['argv'][1] == 'dbg')
	{
		return true;
	}

	$currentTime = strtotime(date('H:i'), 0);

	foreach($gameSettings->getLotterySettings() as $game)
	{
		if($currentTime >= $game['StartTime'])
		{
            $gameSettings = $game;
			$lotteryTime = strtotime(date("Y-m-d"))+$game['StartTime'];
			$SQL = 'SELECT
				Id,Ready
			FROM
				Lotteries
			WHERE
				Date = '.$lotteryTime.'
			LIMIT 1';
			$lottery = DB::Connect()->query($SQL)->fetch();
			if (!$lottery) {
				$gameSettings['lotteryTime'] = $lotteryTime;
				return true;
			} else {
				if (!$lottery['Ready']) {
					$gameSettings['lotteryId'] = $lottery['Id'];
					$gameSettings['lotteryTime'] = $lotteryTime;
					return true;
				}
			}
		}
	}
	return false;
}

/**
 * Unserialize combination in tickets and update B* fields in tickets fter rollBackTicket proc
 * @throws DBExeption
 */
function SetSerializeBallsRollBack()
{
	$time = microtime(true);
	echo 'rollBack (serialize): '.PHP_EOL;
	$SQL = "SELECT
			Id AS id,
			Combination AS combination
		FROM
			LotteryTickets
	";
	DB::Connect()->query($SQL)->fetchAll(PDO::FETCH_FUNC, function($id, $combination)
	{
		$comb = unserialize($combination);
		$filds	= array();

		foreach((array)$comb as $ball)
		{
			$filds[]= 'B'.((int)$ball);
		}
		DB::Connect()->query("UPDATE LotteryTickets SET ".implode('=1, ', $filds)."=1 WHERE id=".$id);
	});
	echo (microtime(true) - $time).PHP_EOL;
}

/**
 * rollback fot last lottery
 * @param $text #error description
 */
function RollBack($text = '') {
	try {
		echo PHP_EOL . $text . PHP_EOL;
		$time = microtime(true);
		echo 'rollBack: ' . PHP_EOL;
		DB::Connect()->query("CALL rollBackLotteryLast");
		echo (microtime(true) - $time) . PHP_EOL;
		SetSerializeBallsRollBack();
	} catch (Exception $e) {
		echo "rollBack get exception: ". PHP_EOL . $e->getMessage().PHP_EOL;
		if(file_exists($tmp = __DIR__.'/lottery.lock.tmp')) {
			unlink($tmp);
		}
		exit();
	}
}

/**
 * Start ApplyLotteryCombination and restart if catch Exception $counter tries
 * @param $comb
 */
function ApplyLotteryCombinationAndCheck(&$comb)
{
	static $counter = 0;

	$roll = function($text) use ($comb, &$counter)
	{
		$times = 1;
		echo 'rollBack start: ';
		RollBack($text);
		$counter++;
		if ($counter < $times) {
			sleep(5);
			ApplyLotteryCombinationAndCheck($comb);
		} else {
			echo "rollBack is looped $times times: exit" . PHP_EOL;
			if (file_exists($tmp = __DIR__ . '/lottery.lock.tmp')) {
				unlink($tmp);
			}
		}
	};

    try {
		ApplyLotteryCombination($comb);
	} catch (Exception $e) {
		$roll(PHP_EOL . 'HoldLottery is catch' . PHP_EOL . $e->getMessage() . PHP_EOL);
	}

	return $comb;
}

function ApplyLotteryCombination(&$comb)
{
	if (!$comb) {
		return;
	}
	$lid = (int)$comb['id'];

	echo 'ApplyLotteryCombination' . PHP_EOL;
	$time = microtime(true);

	DB::Connect()->query("CALL applyLotteryLast");
	echo (microtime(true) - $time).PHP_EOL;

	DB::Connect()->query("UPDATE Lotteries SET Ready = 1 WHERE Id = $lid");

    if(SettingsModel::instance()->getSettings('counters')->getValue('MONEY_ADD_INCREMENT')){
        $counters=SettingsModel::instance()->getSettings('counters')->getValue();
        $counters['MONEY_ADD']+=$counters['MONEY_ADD_INCREMENT'];
        SettingsModel::instance()->getSettings('counters')->setValue($counters)->create();
    }

	echo PHP_EOL.'recache: '.PHP_EOL;
	$time = microtime(true);
	LotteriesModel::instance()->recache();
	echo (microtime(true) - $time).PHP_EOL;
	echo PHP_EOL.PHP_EOL;

	unset($comb['fields']);
}

function SetLotteryCombination($comb, $simulation, $lastTicketId)
{
	global $gameSettings;

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
    $comb['Combination'] = $Combination;


	$ballsArray = array_flip(range(1, $_ballsCount));

    $comb['ballsArray'] = array_intersect_key($comb, $ballsArray);


	$SQL = 'SELECT
				COUNT(DISTINCT(PlayerId))
			FROM
				LotteryTickets
			WHERE
				LotteryId = 0
				AND
				Id <= '.$lastTicketId.'
				AND ('.implode(' OR ', $where).')';
    $comb['WinnersCount'] = current(DB::Connect()->query($SQL)->fetch());

    if(!$simulation) {

        $SQL = "INSERT INTO Lotteries
				(`Date`, Combination, LastTicketId, WinnersCount, MoneyTotal, PointsTotal, BallsTotal, %s)
			VALUES
				(%d, '%s', %d, %d, %f, %d, '%s', 1, 1, 1, 1, 1, 1)";

        $SQL = sprintf($SQL,	implode(',', $comb['fields']),
			$gameSettings['lotteryTime'],
            serialize($Combination),
			$lastTicketId,
            $comb['WinnersCount'],
            $comb['MoneyTotal'],
            $comb['PointsTotal'],
            serialize($comb['ballsArray']));

        DB::Connect()->query($SQL);
        $comb['id']           = DB::Connect()->lastInsertId();
    }


	echo (microtime(true) - $time).PHP_EOL;

	return $comb;
}

function GetLotteryCombinationStatistics($lastTicketId)
{
	global $_variantsCount;

	$time = microtime(true);
	echo 'GetLotteryCombinationStatistics: ';

	$fields = array();

	for($i = 1; $i <= $_variantsCount; $i++)
	{
		$fields[]= "COUNT(B$i) as B$i";
	}

	$SQL    = sprintf('SELECT COUNT(DISTINCT(PlayerId)) PlayersTotal, COUNT(*) TicketsTotal, %s FROM LotteryTickets WHERE Id <= '.$lastTicketId.' AND LotteryId = 0', implode(',', $fields));
	$stats  = DB::Connect()->query($SQL)->fetch();

	asort($stats);

	echo (microtime(true) - $time).PHP_EOL.PHP_EOL;

	$echo = array();
	foreach($stats as $ball => $count)
	{
        if(!in_array($ball,array('PlayersTotal','TicketsTotal')))
		$echo[]= str_pad($ball, 3, '_', STR_PAD_RIGHT).":$count";
	}
	$echo = implode(', ', $echo);
	echo wordwrap($echo);

	echo PHP_EOL.PHP_EOL.PHP_EOL;

	return $stats;
}

function GetLotteryCombination($ballsStart, $ballsRange, $rounds, $return, $orderBy, $lastTicketId)
{
	global $_ballsCount;
	global $_variantsCount;

	static $cache = array();

	$ballsStart = min($ballsStart, $_variantsCount - $_ballsCount);
	$ballsRange+= $_ballsCount;
	$ballsRange = min($ballsRange, $_variantsCount - $ballsStart);

	$stats = GetLotteryCombinationStatistics($lastTicketId);

	if(!array_sum($stats))
	{
		echo 'Tickets is not found.'.PHP_EOL;

		return null;
	}

    $total=array(
        'PlayersTotal'=>$stats['PlayersTotal'],
        'TicketsTotal'=>$stats['TicketsTotal']
    );
    unset ($stats['PlayersTotal'],$stats['TicketsTotal']);

	$time = microtime(true);
	echo 'GetLotteryCombination:'.PHP_EOL;

	$ballsStatSQL = array();
	for($i = 1; $i <= $_ballsCount; $i++)
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
                        AND
                        	Id <= %d
                    )	sm

                    WHERE
                        sm.BallsCount <> 0
                    GROUP BY
                        sm.BallsCount
                )
                stat  INNER JOIN LotterySettings ls USING(BallsCount)

                WHERE
                    ls.CountryCode = 'UA'";

		$SQL = sprintf($SQL, implode(',', $ballsStatSQL), implode('+', $fields), $lastTicketId);

		$rountdStats = DB::Connect()->query($SQL)->fetch();

		$rountdStats['combination'] = $balls;
        $rountdStats+= $total;

		$rountdsStats[(int)$rountdStats[$orderBy]] = $rountdStats;

		echo '  check candidate [ '.implode(', ', $balls).' ] ('.number_format($rountdStats[$orderBy]).'): '.(microtime(true) - $t).PHP_EOL;

		$cache[$hash]= true;
	}

	ksort($rountdsStats);   $rountdsStats = array_values($rountdsStats);

	$return = min($return, count($rountdsStats)-1);

	echo 'Total: '.(microtime(true) - $time).PHP_EOL;

	return $rountdsStats[$return];
}

function HoldLottery($ballsStart = 0, $ballsRange = 3, $rounds = 250, $return = 0, $orderBy = 'MoneyTotal', $simulation=false)
{
	$time = microtime(true);
    $lastTicketId = 0;

	/**
	 * Get lastTicketId
	 */
	$SQL = 'SELECT
				Id
			FROM
				LotteryTickets
			WHERE
				LotteryId = 0
			ORDER BY id DESC
			LIMIT 1';
    
    if($lastTickets = DB::Connect()->query($SQL)->fetch()){
	    $lastTicketId = current($lastTickets);
    }

	$comb = GetLotteryCombination($ballsStart, $ballsRange, $rounds, $return, $orderBy, $lastTicketId);
	$comb = SetLotteryCombination($comb, $simulation, $lastTicketId);
    if($simulation) {print_r($comb);return;}
    $filename = __DIR__.'/../lastLottery';
    try {
        file_put_contents($filename, json_encode(array(
            'i' => isset($comb['id']) ? $comb['id'] : 0,
            'c' => $comb['Combination'],
            'pt' => $comb['PlayersTotal'],
            'pw' => $comb['WinnersCount'],
            'tt' => $comb['TicketsTotal'],
            'tw' => $comb['TicketsCount'],
            'b' => $comb['ballsArray']
        )));
        chmod($filename, fileperms($filename) | 128 + 16 + 2);
    } catch (ErrorException $e){}
	ApplyLotteryCombinationAndCheck($comb);

	echo PHP_EOL.PHP_EOL;   print_r($comb);

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

function HoldLotteryAndCheck($ballsStart = 0, $ballsRange = 3, $rounds = 250, $return = 0, $orderBy = 'MoneyTotal', $simulation=false)
{
	HoldLottery($ballsStart, $ballsRange, $rounds, $return, $orderBy, $simulation);
}

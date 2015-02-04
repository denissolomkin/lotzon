<?php

require_once('lottery.inc.php');

if($_SERVER['argv'][1] == 'dbg')
{
	require_once('lottery.inc.php');

//	ConverDB();
//	RestoreAllTickets();

	$rounds     = 50;
	$ballsRange = 40;

	HoldLottery(0, 0, $ballsRange, $rounds);
	sleep(20);

	LotterySimulation('simulation.html', 0, $ballsRange, $rounds);

}

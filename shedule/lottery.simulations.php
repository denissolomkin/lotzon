<?php

if($_SERVER['argv'][1] == 'dbg')
{
	require_once('lottery.inc.php');

//	ConverDB();
//	RestoreAllTickets();

	HoldLottery(null);
//	sleep(10);
//	LotterySimulation();

}

<?php

if($_SERVER['argv'][1] == 'dbg')
{
	require_once('lottery.inc.php');

//	ConverDB();
//	RestoreAllTickets();

	HoldLottery(0, $ballsStart = 0, $ballsRange = 20, $rounds = 20);

}

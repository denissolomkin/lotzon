<?php
require_once('init.php');

$sql = "DELETE FROM `PlayerPing` WHERE `Ping` < :ping";
$sth = DB::Connect()->prepare($sql);
$sth->execute(array(
    ':ping' => (time()-(SettingsModel::instance()->getSettings('counters')->getValue('PLAYER_TIMEOUT')?:300))
));

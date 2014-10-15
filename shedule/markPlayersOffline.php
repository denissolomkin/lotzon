<?php
require_once('init.php');

DB::Connect()->query("UPDATE `Players` SET `Online` = 0 WHERE `OnlineTime` < " . (time() - Config::instance()->playerOfflineTimeout));
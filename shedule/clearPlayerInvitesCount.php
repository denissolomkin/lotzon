<?php

require_once('init.php');

DB::Connect()->query("UPDATE `Players` SET `InvitesCount` = 10");
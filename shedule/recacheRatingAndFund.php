<?php
require_once('init.php');
GameAppsModel::instance()->incrementGameTop();
GameAppsModel::instance()->recacheRatingAndFund();
GamePlayersModel::instance()->updateBotsPing();

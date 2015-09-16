<?php
require_once('init.php');
OnlineGamesModel::instance()->incrementGameTop();
OnlineGamesModel::instance()->recacheRatingAndFund();

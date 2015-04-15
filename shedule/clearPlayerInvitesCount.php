<?php

require_once('init.php');

$_socialPostsCount = array('vkontakte'=>1,'facebook'=>1,'twitter'=>1,'odnoklassniki'=>1,'plusone'=>1);

DB::Connect()->query("UPDATE `Players` SET `InvitesCount` = 10, `SocialPostsCount` = '".serialize($_socialPostsCount)."'");
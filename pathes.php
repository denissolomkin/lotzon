<?php

define('PATH_ROOT', realpath(dirname(__FILE__)) . '/');

define('PATH_PROTECTED', PATH_ROOT . 'protected/');

define('PATH_CONFIGS', PATH_ROOT . 'protected/configs');

define('PATH_SYSTEM', PATH_PROTECTED . 'system/');

define('PATH_APPLICATION', PATH_PROTECTED . 'application/');

define('PATH_TEMPLATES', PATH_PROTECTED . 'templates/');

define('PATH_CONTROLLERS', PATH_APPLICATION . 'controllers/');

define('PATH_INTERFACES', PATH_APPLICATION . 'model/interfaces/');

define('PATH_GAMES', PATH_APPLICATION . 'model/games/');

define('PATH_FILESTORAGE', PATH_ROOT . 'filestorage/');

define('PATH_MMDB_FILE', PATH_ROOT . 'mmdb/GeoIP2-Country.mmdb');
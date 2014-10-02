<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

/*if (PHP_SAPI != 'cli') {
    die ('forbidden');
}*/

// load pathes 
require_once dirname(__FILE__) . '/../pathes.php';
// load application class
require_once PATH_APPLICATION . 'Application.php';

require dirname(__FILE__) . '/../vendor/autoload.php';

// load configs
Application::import(PATH_SYSTEM . 'Config.php');
Application::import(PATH_SYSTEM . '*');
Application::import(PATH_CONFIGS . '*');
Application::import(PATH_CONTROLLERS . '*');

?>

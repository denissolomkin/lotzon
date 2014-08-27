<?php
    
error_reporting(E_ALL);
ini_set('display_errors', 1);

// load configs
$confs = glob(dirname(__FILE__) . '/protected/configs/*.php');
foreach ($confs as $conf) {
    require_once($conf);
}

// init applicatioin
require_once PATH_APPLICATION . 'Application.php';
Application::init();


$ac = new Player();
?>
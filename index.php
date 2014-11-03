<?php
if ($_SERVER['REQUEST_URI'] != '/trailer' && $_SERVER['REQUEST_URI'] != '/trailer/') {
    if (!in_array($_SERVER['REMOTE_ADDR'], array('212.98.168.40', '93.75.35.202','188.64.170.221', '192.168.1.88','192.168.100.153','188.64.171.181','127.0.0.1','81.20.200.182','79.139.133.100','82.146.41.129','94.228.204.10', '193.232.184.141','46.174.161.159','77.91.183.217', '93.72.151.96', '193.239.152.37', '188.231.153.219', '212.90.61.122', '212.42.94.154', '212.90.61.55', '195.211.145.115', '176.36.137.116'))) {
        header("Location: /trailer");

        exit;
    }    
}

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);

// load pathes 
require_once dirname(__FILE__) . '/pathes.php';
// load application class
require_once PATH_APPLICATION . 'Application.php';

require 'vendor/autoload.php';

// load configs
Application::import(PATH_SYSTEM . 'Config.php');
Application::import(PATH_SYSTEM . '*');
Application::import(PATH_CONFIGS . '*');
Application::import(PATH_CONTROLLERS . '*');


$dispatcher = new \SlimController\Slim(array(
    'view'                       => '\Slim\LayoutView',
    'templates.path'             => PATH_TEMPLATES,
    'controller.method_suffix'   => 'Action',
    'controller.template_suffix' => 'php',
));


$dispatcher->addRoutes(Config::instance()->privateResources);
$dispatcher->addRoutes(Config::instance()->publicResources);

$dispatcher->run();
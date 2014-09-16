<?php
    
error_reporting(E_ALL);
ini_set('display_errors', 1);

// load pathes 
require_once dirname(__FILE__) . '/pathes.php';
// load application class
require_once PATH_APPLICATION . 'Application.php';

require 'vendor/autoload.php';

// load configs
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
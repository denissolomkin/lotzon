<?php
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcacheSessionHandler;

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);
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


$memcache = new Memcache;
$memcache->connect(Config::instance()->memcacheHost, 11211);
$storage = new NativeSessionStorage(array(), new MemcacheSessionHandler($memcache));
$session = new Session($storage);
$session->start();

$dispatcher = new \SlimController\Slim(array(
    'view'                       => '\Slim\LayoutView',
    'templates.path'             => PATH_TEMPLATES,
    'controller.method_suffix'   => 'Action',
    'controller.template_suffix' => 'php',
));


$dispatcher->addRoutes(Config::instance()->privateResources);
$dispatcher->addRoutes(Config::instance()->publicResources);

$dispatcher->run();
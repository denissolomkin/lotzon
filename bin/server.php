<?php
namespace controllers\production;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Session\SessionProvider;
use Ratchet\WebSocket\WsServer;
use React\Socket\Server;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcacheSessionHandler;
use \Config, \Application, \React\EventLoop\Factory;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/pathes.php';
require_once PATH_APPLICATION . 'Application.php';
require_once PATH_APPLICATION . '/controllers/production/WebSocketController.php';
Application::import(PATH_SYSTEM . '*');
Application::import(PATH_CONFIGS . '*');

/*
 require_once dirname(__DIR__) . '/../../system/Cache.php';
 require_once dirname(__DIR__) . '/../../system/DB.php';
 require_once dirname(__DIR__) . '/../../system/Config.php';
 require_once dirname(__DIR__) . '/../../../protected/configs/config.php';
 */
$memcache = new \Memcache;
$memcache->connect('localhost', 11211);

$loop = Factory::create();
$webSock = new Server($loop);
$webSock->listen(Config::instance()->wsServerPort, '0.0.0.0');

$server = new IoServer(
    new HttpServer(new WsServer(
        new SessionProvider(
            new WebSocketController($loop),
            new MemcacheSessionHandler($memcache)
        )
        )
    ), $webSock
);


$loop->run();

/*
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new SessionProvider(
                new WebSocketController(null),
                new MemcacheSessionHandler($memcache)
            )
        )
    ),8080
);
$server->run();
*/
<?php namespace WebSocket;//\WebSocketController;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Session\SessionProvider;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcacheSessionHandler;

require dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__FILE__) . '/../pathes.php';
// load application class
require_once PATH_APPLICATION . 'Application.php';

\Application::import(PATH_APPLICATION . '/controllers/production/WebSocketController.php');

$memcache = new \Memcache;
$memcache->connect('localhost', 11211);
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new SessionProvider(
                new WebSocketController,
                new MemcacheSessionHandler($memcache)
            )
        )
    )
    ,8080
);
$server->run();
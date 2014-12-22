<?php

namespace controllers\production;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Session\SessionProvider;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcacheSessionHandler;


class WebSocketServer
{
    public function runAction()
    {
//        file_get_contents('http://localhost/bin/server.php');
        require_once dirname(__DIR__) . '/../../../vendor/autoload.php';
        require_once dirname(__DIR__) . '/../../../pathes.php';
// load application class
        require_once PATH_APPLICATION . 'Application.php';
//echo dirname(__DIR__);die();
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
    }
}
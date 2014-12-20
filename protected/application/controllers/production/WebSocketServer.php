<?php

namespace controllers\production;


class WebSocketServer
{
    public function runAction()
    {
        @file_get_contents('http://localhost/bin/server.php');
        //require_once './bin/server.php';
    }
}



/*
namespace controllers\production;

use WebSocket\WebSocketController;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Session\SessionProvider;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcacheSessionHandler;
use \Memcache;

// load application class
require_once PATH_APPLICATION . 'Application.php';

class WebSocketServer
{
    public function runAction()
    {
        $memcache = new Memcache;
        $memcache->connect('localhost', 11211);
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new SessionProvider(
                        new WebSocketController(),
                        new MemcacheSessionHandler($memcache)
                    )
                )
            )
            , 8080
        );
        $server->run();
    }
}
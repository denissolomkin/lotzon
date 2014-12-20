<?php

namespace controllers\production;


class WebSocketServer
{
    public function runAction()
    {
        @file_get_contents('http://localhost/bin/server.php');
    }
}
<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Chat;
$dir=dirname(__DIR__) . '/vendor/autoload.php';
require $dir;

echo $dir;
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);
echo "Started";
$server->run();

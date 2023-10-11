<?php
//Ratchet library
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use PISSProject\OnlineTable;

    // include these core files
    require dirname(__DIR__) . '/vendor/autoload.php';
    require dirname(__DIR__) . '/php/ServerHandler.php';

    //We listen to the set ip on port 8090
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new OnlineTable()
            )
            ),
            8090
    );

    //Start server
    $server->run();
?>
<?php

$http_header = "";

$msg = "Hello socket word!!";

$host = '127.0.0.1';
$port = 8080;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, $host, $port);
socket_listen($socket);
socket_set_nonblock($socket);
socket_get_option($socket, SOL_TCP, SO_DEBUG);

echo "Hosting in $host:$port..." . PHP_EOL;
while(1){
    if($newClient = socket_accept($socket)) {
        echo "New client loggin in $host:$port" . PHP_EOL;
        echo socket_read($newClient, 3000);
        socket_send($newClient, $msg, strlen($msg), MSG_CONFIRM);
        socket_close($newClient);
    }
}
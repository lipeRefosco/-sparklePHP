<?php

function main() {
    $header = "HTTP/1.1 200 OK";
    $separator = PHP_EOL . PHP_EOL;
    $msg = "Hello socket word!!";
    $response = $header . $separator . $msg;

    $host = 'localhost';
    $port = 8080;
    
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_set_nonblock($socket);
    socket_bind($socket, $host, $port);
    socket_listen($socket);

    echo "Hosting in $host:$port..." . PHP_EOL;
    while(1){
        if($newClient = socket_accept($socket)) {

            socket_getpeername($newClient, $host, $port);
            echo "New client loggin in $host:$port" . PHP_EOL;         

            // Read all avalable data from the socket
            echo socket_read($newClient, 3000);
            
            // Send a response to the client connected
            socket_send($newClient, $response, strlen($response), MSG_CONFIRM);
            
            // Turn off the client connection
            socket_shutdown($newClient);
        }
    }
}

main();
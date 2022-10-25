<?php
namespace SparklePHP\Socket;

class Socket {

    private $socket;
    public string $addres;
    public int $port;
    private array $clients;

    function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    }

    public function listen(callable $callback): void
    {
        $callback($this);

        var_dump($this);

        socket_bind($this->socket, $this->addres, $this->port);
        socket_listen($this->socket, SOMAXCONN);
        
        while(1){
            $this->clients[] = socket_accept($this->socket);
            
            if(count($this->clients) === 0) continue;

            foreach($this->clients as $client) {
                // echo socket_read($client, 3000);
                $msg = 'HTTP/1.1 200 OK' . PHP_EOL . PHP_EOL . 'teste';
                socket_send($client, $msg, strlen($msg), MSG_EOF);
                socket_close($client);
                array_shift($this->clients);
            }
        }
    }
}
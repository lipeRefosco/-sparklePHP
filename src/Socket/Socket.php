<?php
namespace SparklePHP\Socket;

use Socket as GlobalSocket;

class Socket {

    private GlobalSocket $socket;
    public string $addres;
    public int $port;
    private array $clients;

    function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $this->clients = [];
    }

    public function listen(callable $callback): void
    {
        $callback($this);

        // var_dump($this);
        socket_set_nonblock($this->socket);
        socket_bind($this->socket, $this->addres, $this->port);
        socket_listen($this->socket, SOMAXCONN);
        
        $this->loop();
    }
    
    private function loop(): void
    {
        // $teste = count($this->clients);
        while(1){

            // echo "new client $teste" . PHP_EOL;
            if($newClient = socket_accept($this->socket)) {
                
                $this->clients[] = $newClient;

                foreach($this->clients as $client) {
                    
                    echo 'on array ' . PHP_EOL;
                    var_dump($client);

                    // echo socket_read($client, 3000);
                    $msg = 'HTTP/1.1 200 OK' . PHP_EOL .
                    'Content-Type: application/json' .
                    PHP_EOL . PHP_EOL .
                    json_encode([
                        ["key" => "value"],
                        ["teste" => 1]
                    ]);
                    socket_send($client, $msg, strlen($msg), MSG_EOF);
                    socket_close($client);
                    array_shift($this->clients);
                }
            }
            // break;
        }
    }
}
<?php
namespace SparklePHP\Socket;

use Exception;
use Socket as GlobalSocket;

class Socket {

    protected GlobalSocket $socket;
    protected array $clients;

    function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $this->clients = [];
    }
    
    protected function bind(GlobalSocket $socket, string $address, int $port): bool
    {
        return socket_bind($socket, $address, $port);
    }
    
    protected function setBind(): void
    {
        try {
            $this->bind($this->socket, $this->address, $this->port);
        }catch(Exception $e){
            socket_close($this->socket);
            $this->setBind();
            // echo $e->getMessage();
        }
    }

    protected function set_nonblock(GlobalSocket $socket): void
    {
        socket_set_nonblock($socket);
    }
    
    protected function loop(callable &$protocol): void
    {
        while (true) {
            $protocol();
        }
    }

    protected function listen(): void
    {
        socket_listen($this->socket);
    }

    protected function accept(GlobalSocket $socket): void
    {
        if($this->clients[] = socket_accept($socket));
    }
    protected function read(GlobalSocket &$socket, int $limit): string
    {
        return socket_read($socket, $limit);
    }

    static function send(GlobalSocket $client, string $data, ?int $flag = MSG_CONFIRM):void
    {
        socket_send($client, $data, strlen($data), $flag);
    }

    static function close(GlobalSocket $client):void
    {
        socket_close($client);
    } 

}
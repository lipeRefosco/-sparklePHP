<?php
namespace SparklePHP\Socket;

use Exception;
use Socket as GlobalSocket;

class Socket {

    protected GlobalSocket $socket;
    protected array $clients;
    protected string $address;
    protected string $port;

    function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $this->clients = [];
    }
    
    protected function bind(GlobalSocket &$socket, string $address, int $port): bool
    {
        return socket_bind($socket, $address, $port);
    }
    
    protected function setBind(): void
    {
        $this->setOptions();
        $this->bind($this->socket, $this->address, $this->port);
    }

    protected function setOptions(): void
    {
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, ["sec" => 1, "usec" => 0]);
    }

    protected function setNonblock(): void
    {
        socket_set_nonblock($this->socket);
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

    protected function accept(GlobalSocket &$socket): void
    {
        if($hasNewClient = socket_accept($socket)) {
            $this->clients[] = $hasNewClient;
        }
    }
    protected function read(GlobalSocket &$socket, int $limit): string
    {
        return socket_read($socket, $limit);
    }

    static function send(GlobalSocket &$client, string $data, ?int $flag = MSG_CONFIRM):void
    {
        socket_send($client, $data, strlen($data), $flag);
    }

    static function close(GlobalSocket &$client):void
    {
        socket_close($client);
    }

    public function removeFirstClient(): void
    {
        array_shift($this->clients);
    }

}
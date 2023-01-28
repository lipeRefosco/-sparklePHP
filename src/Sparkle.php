<?php

namespace SparklePHP;

use SparklePHP\Socket\Protocol\Http\HttpServer;

class Sparkle extends HttpServer {

    function __construct(string $address, int $port)
    {
        parent::__construct();
        $this->address = $address;
        $this->port = $port;
    }

    public function __call(string $method, mixed $arguments)
    {
        [$route, $script] = $arguments;
        
        $this->router->set($route, $method, $script);
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPort(): int
    {
        return $this->port;
    }

}
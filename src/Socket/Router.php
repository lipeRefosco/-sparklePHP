<?php

namespace SparklePHP\Socket;

class Router
{
    private array $routes;

    function __construct()
    {
        $this->routes = [];
        $this->set("default", "all", function($_, $res) {
            $res->send("default page!");
        });
    }

    public function set(string $route, string $method, callable $execution): void
    {
        $method = strtoupper($method);

        $this->routes[$route] = [
            $method => $execution
        ];
    }

    public function hasEndpoint(string $route, string $method): bool
    {
        if( !$this->routeExist($route) ) return false;
        if( !$this->httpMethodOnRoute($method, $route) ) return false;
        
        return true;
    }

    private function routeExist(string $route): bool
    {
        return array_key_exists($route, $this->routes);
    }

    private function httpMethodOnRoute(string $method, string $route): bool
    {
        return array_key_exists($method, $this->routes[$route]);
    }

    public function getEndpoint(string $route, string $method): callable
    {
        $method = strtoupper($method);
         
        return $this->routes[$route][$method];
    }
}
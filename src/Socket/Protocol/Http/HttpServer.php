<?php

namespace SparklePHP\Socket\Protocol\Http;

use DateTime;
use Exception;
use SparklePHP\Socket\Socket;
use SparklePHP\Socket\Protocol\Http\Request;
use SparklePHP\Socket\Protocol\Http\Response;
use SparklePHP\Socket\Router;

class HttpServer extends Socket {
    
    protected int $limit;
    protected Request $request;
    protected Response $response;
    protected Router $router;

    function __construct()
    {
        parent::__construct();
        $this->router = new Router();
        $this->router->setDefault();
        $this->limit = 1000;
    }

    public function protocol(): void
    {
        $this->accept($this->socket);

        foreach($this->clients as $client) {
            
            try {
                $rawRequest = $this->read($client, $this->limit);

                if(!is_string($rawRequest) || $rawRequest == "") throw new Exception("bad request");

                $this->request = new Request($rawRequest);
                $this->request->parseRaw();

                $this->response = new Response();
                $this->response->setup();
                
                $requestRoute = $this->request->headers->route;
                $requestMethod = $this->request->headers->method;

                $endpoint = $this->router->hasEndpoint($requestRoute, $requestMethod)
                          ? $this->router->getEndpoint($requestRoute, $requestMethod)
                          : $this->router->getEndpoint("default", "all");

                $endpoint($this->request, $this->response);

                $this->response->toRaw();
                
                Socket::send($client, $this->response->raw);
                
            } catch (Exception $e) {
                Socket::send($client, json_encode($e));
            } finally {
                Socket::close($client);
                $this->removeFirstClient();
            }
        }
    }

    public function listen(?callable $callback = null): void
    {
        parent::setBind();
        parent::setNonblock();
        parent::listen();

        is_null($callback) ?: $callback($this);

        while(true) $this->protocol();
    }
}
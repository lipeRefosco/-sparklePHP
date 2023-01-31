<?php

namespace SparklePHP\Socket\Protocol\Http;

class Response {
    
    public string $raw;
    public Headers $headers;
    public Body $body;
    public string $separator = PHP_EOL;

    function __construct()
    {
        $this->headers = new Headers();
        $this->body = new Body();
    }

    public function send(string $data): void
    {
        // function isJson(&$string) {
        //     return is_object(json_decode($string)) || is_array(json_decode($string)) ? true : false;
        // }
        
        // if(isJson($data)) $this->headers->set("Content-Type", "application/json");
        
        $this->body->set("raw", $data);
    }

    public function sendJSON(string | array | object $json): void
    {
        $this->headers->set("Content-Type", "application/json");
        $this->send(json_encode($json));
    }

    public function sendHTML(string $html): void
    {
        $this->headers->set("Content-Type", "text/html; charset=UTF-8");
        $this->send($html);
    }

    public function toRaw(): void
    {
        $this->headers->toRaw();

        $this->raw = $this->headers->raw;

        $this->raw .= $this->separator;
        
        $this->raw .= $this->body->raw;
    }

    public function setup(): void
    {
        $this->headers->setVersion("HTTP/1.1");
        $this->headers->setStatus("200");
    }

}
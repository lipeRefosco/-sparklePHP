<?php

namespace SparklePHP\Socket\Protocol\Http;

class Response {
    
    public string $raw;
    public Headers $headers;
    public Body $body;
    public string $separator = PHP_EOL . PHP_EOL;

    function __construct()
    {
        $this->headers = new Headers("");
        $this->body = new Body("");
    }

    public function send(string | array $data): void
    {
        $this->body->set("data", $data);
    }

    public function toRaw(): void
    {
        $this->headers->toRaw();
        $this->body->toRaw();

        $this->raw = $this->headers->raw;

        is_null($this->body->data) ?: $this->raw .= $this->separator;
        
        $this->raw .= $this->body->raw;
    }

    public function setDefault(): void
    {
        $this->headers->set("version", "HTTP/1.1");
        $this->headers->set("status", "200");
    }

}
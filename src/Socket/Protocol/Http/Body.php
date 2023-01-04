<?php

namespace SparklePHP\Socket\Protocol\Http;

use Error;

class Body{

    public string $raw;
    public string | array $data;

    function __construct(string $raw = "")
    {
        $this->raw = trim($raw);
        $this->data = "";
    }

    public function set(string $key, string | array $data): void
    {
        $this->$key = $data;
    }
    
    public function parseRawByContentType(string | null $contentType): void
    {   
        if($contentType === "application/json") {
            $this->data = json_decode($this->raw, true);
            return;
        }

        $this->data = $this->raw;
    }

    public function toRaw(string | null $contentType): void
    {
        if($contentType === "application/json") {
            $this->raw = json_encode($this->data);
            return;
        }

        $this->raw = $this->data;
        return;
    }
}
<?php

namespace SparklePHP\Socket\Protocol\Http;

use Error;

class Body{

    public string $raw;
    public string | array $data;

    function __construct(string $raw = null)
    {
        $this->raw = trim($raw);
    }

    public function set(string $key, string | array $data): void
    {
        $this->$key = $data;
    }
    
    public function parseRawByContentType(?string $contentType): void
    {   
        if($contentType === "application/json") {
            $this->data = json_decode($this->raw, true);
            return;
        }

        $this->data = $this->raw;
    }

    public function toRaw(): void
    {
        is_string($this->data)
        ? $this->raw = $this->data
        : $this->raw = json_encode($this->data);
    }
}
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
        $this->raw = json_encode($this->data);
        return;
    }
}
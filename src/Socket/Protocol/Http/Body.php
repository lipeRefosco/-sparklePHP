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

    /**
     * @throws Error
    */
    public function parseRawByContentType(): void
    {
        if(is_null($this->contentType)) throw new Error("No Content-Type");
        
        if($this->contentType === "application/json") {
            $this->data = json_decode($this->raw, true);
            return;
        }

        $this->data = $this->raw;
    }

    public function toRaw(): void
    {
        if($this->contentType === "application/json") {
            $this->raw = json_encode($this->data);
            return;
        }

        $this->raw = $this->data;
        return;
    }
}
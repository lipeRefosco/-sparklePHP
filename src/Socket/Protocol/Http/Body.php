<?php

namespace SparklePHP\Socket\Protocol\Http;

class Body{

    public string $raw;

    function __construct(string $raw)
    {
        $this->raw = trim($raw);
    }
}
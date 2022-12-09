<?php

namespace SparklePHP\Socket\Protocol\Http;

class Headers {

    public string $raw;
    public array  $rawSplited;
    public string $method;
    public string $route;
    public string $version;

    function __construct(string $raw)
    {
        $this->raw = trim($raw);
        $this->rawSplited = self::splitRaw($raw);
    }

    static private function splitRaw(string $raw): array
    {
        return array_map( fn($string) => trim($string),
                          explode("\n", trim($raw))
                        );
    }

    public function parseRaw(): void
    {
        $this->setMethod();
        $this->setRoute();
        $this->setHttpVersion();
        $this->setFieldsAndValues();
    }

    private function setMethod():void
    {
        [$method] = explode(" ", $this->rawSplited[0]);
        
        $method = strtoupper($method);

        $this->method = $method;
    }

    private function setRoute(): void
    {
        [$_, $route] = explode(" ", $this->rawSplited[0]);

        $this->route = $route;
    }

    private function setHttpVersion(): void
    {
        [$_, $_, $httpVersion] = explode(" ", $this->rawSplited[0]);

        $this->version = $httpVersion;
    }

    private function setFieldsAndValues(): void
    {
        $rawSplited = array_slice($this->rawSplited, 1);
        
        foreach($rawSplited as $value) {
            [$key, $val] = explode(": ", $value);
            $this->set($key, $val);
        }
    }
    
    public function set(string $key, string $value): void
    {
        $key = strtolower($key);

        $this->$key = $value;
    }
}
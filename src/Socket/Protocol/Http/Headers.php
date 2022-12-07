<?php

namespace SparklePHP\Socket\Protocol\Http;

class Headers {

    public string $method;
    public string $route;
    public string $version;

    function __construct(string $raw)
    {
        [$method, $route, $version, $headers] = self::parseHeaders($raw);
        $this->method = strtolower($method);
        $this->route = strtolower($route);
        $this->version = strtolower($version);

        foreach ($headers as $key => $value) $this->set($key, $value);
    }
    
    static function parseHeaders(string $raw): array
    {
        $rawSplited = explode("\n", trim($raw));
        $shiftIndice = fn(&$i) => array_shift($i);
        
        [$method, $route, $version] = self::extractMethodRoteVersion($rawSplited[0]);

        $shiftIndice($rawSplited);

        $headers = [];
        foreach($rawSplited as $value) {
            [$key, $val] = explode(": ", $value);
            $key = $key;
            $val = $val;
        
            $headers += [$key => $val];
        }

        return [$method, $route, $version, $headers];
    }

    static private function extractMethodRoteVersion(string &$firstRowHttpHeader): array
    {
        return explode(" ", $firstRowHttpHeader);
    }
    
    public function set(string $key, string $value): void
    {
        $key = strtolower($key);

        $this->$key = $value;
    }
}
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
        $removeFirstIndice = fn(&$i) => array_shift($i);

        $rawSplited = array_map(
            fn($string) => trim($string),
            explode("\n", trim($raw))
        );
        [$method, $route, $version] = self::extractMethodRoteVersion($rawSplited[0]);

        $removeFirstIndice( $rawSplited );

        $headers = self::getHeadersFromRaw($rawSplited);

        return [$method, $route, $version, $headers];
    }

    static private function getHeadersFromRaw(array $rawSplited): array
    {
        $headers = [];
        
        foreach($rawSplited as $value) {
            [$key, $val] = explode(": ", $value);
            $headers += [$key => $val];
        }

        return $headers;
    }

    static private function extractMethodRoteVersion(string $firstRowHttpHeader): array
    {
        return explode(" ", $firstRowHttpHeader);
    }
    
    public function set(string $key, string $value): void
    {
        $key = strtolower($key);

        $this->$key = $value;
    }
}
<?php

namespace SparklePHP\Socket\Protocol\Http;

use Exception;

class Headers {

    public string $raw;
    public array  $rawSplited;
    public string $method;
    public string $route;
    public string $status;
    public string $version;
    public array $query;
    public array $fields = [];

    function __construct(string $raw = null)
    {
        if(is_null($raw)) return;

        $this->raw = trim($raw);
        $this->rawSplited = self::splitRaw($raw);
    }

    /**
     * Split the raw request received from client and
     * clean each splited row.
     * 
     * @param string
     * @return array 
    */
    static private function splitRaw(string $raw): array
    {
        $rawSplited = explode("\n", trim($raw));

        return array_map(
            fn($string) => trim($string),
            $rawSplited
        );
    }
    
    public function parseRaw(): void
    {
        $statusLine = $this->rawSplited[0];
        [$method, $fullRoute, $httpVersion] = explode(" ", $statusLine);

        $route = ""; $queryParams = "";
        $this->separateQueryParamsFromRoute($fullRoute, $route, $queryParams);

        $this->setMethod($method);
        $this->setRoute($route);
        $this->setVersion($httpVersion);
        $this->setQueryParams($queryParams);
        $this->setFieldsAndValues();

        unset($this->raw);
        unset($this->rawSplited);
    }
    
    public function set(string $key, string $value): void
    {
        $this->fields += [
            $key => $value
        ];
    }

    public function toRaw(): void
    {
        $this->raw = $this->constructStatusLine() . PHP_EOL;
        
        foreach ($this->fields as $key => $value) {
            $this->raw .= implode(": ", [$key, $value]) . PHP_EOL;
        }
    }

    private function setFieldsAndValues(): void
    {
        $rawSplited = array_slice($this->rawSplited, 1);
        
        foreach($rawSplited as $value) {
            [$key, $val] = explode(": ", $value);
            $this->set($key, $val);
        }
    }

    private function constructStatusLine(): string
    {
        return implode(" ", [
            $this->version,
            $this->status,
            $this->statusCodeToText($this->status)
        ]);
    }

    private function statusCodeToText(string $code): string
    {
        $codes = [
            "100"  => "Continue",
            "101"  => "Switching Protocols",
            "200"  => "OK",
            "201"  => "Created",
            "202"  => "Accepted",
            "203"  => "Non-Authoritative Information",
            "204"  => "No Content",
            "205"  => "Reset Content",
            "206"  => "Partial Content",
            "300"  => "Multiple Choices",
            "301"  => "Moved Permanently",
            "302"  => "Found",
            "303"  => "See Other",
            "304"  => "Not Modified",
            "305"  => "Use Proxy",
            "307"  => "Temporary Redirect",
            "400"  => "Bad Request",
            "401"  => "Unauthorized",
            "402"  => "Payment Required",
            "403"  => "Forbidden",
            "404"  => "Not Found",
            "405"  => "Method Not Allowed",
            "406"  => "Not Acceptable",
            "407"  => "Proxy Authentication Required",
            "408"  => "Request Time-out",
            "409"  => "Conflict",
            "410"  => "Gone",
            "411"  => "Length Required",
            "412"  => "Precondition Failed",
            "413"  => "Request Entity Too Large",
            "414"  => "Request-URI Too Large",
            "415"  => "Unsupported Media Type",
            "416"  => "Requested range not satisfiable",
            "417"  => "Expectation Failed",
            "500"  => "Internal Server Error",
            "501"  => "Not Implemented",
            "502"  => "Bad Gateway",
            "503"  => "Service Unavailable",
            "504"  => "Gateway Time-out",
            "505"  => "HTTP Version not supported"
        ];

        return $codes[$code] ?? $codes["404"];
    }

    private function separateQueryParamsFromRoute(string $fullRoute, string &$route, string &$queryParams): void
    {
        $routeAndQueryParams = explode("?", $fullRoute);
        $route = $routeAndQueryParams[0] ?? throw new Exception("Bad request na separação de strings");
        $queryParams = $routeAndQueryParams[1] ?? ""; 
         
    }

    private function parseQueryParam(string $queryParam): array
    {
        $querySplited = explode("&", $queryParam);
        $queryFormated = [];

        foreach ($querySplited as $queryWithValue) {
            $queryAndValue = explode("=", $queryWithValue);
            $query = $queryAndValue[0];
            $value = $queryAndValue[1] ?? "";
            $queryFormated += [ $query => $value ];
        }

        return $queryFormated;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function setRoute(string $route): void
    {
        $this->route = $route;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    } 

    public function setQueryParams(string $queryParams): void
    {
        $queryIsEmpty = $queryParams == "";
        
        if($queryIsEmpty) {
            $this->query = [];
            return;
        }
      
        $this->query = $this->parseQueryParam($queryParams);
    }
}
<?php

namespace SparklePHP\Socket\Protocol\Http;

class Headers {

    public string $raw;
    public array  $rawSplited;
    public string $method;
    public string $route;
    public string $status;
    public string $version;
    public string $contentType;

    function __construct(string $raw)
    {
        $this->raw = trim($raw);
        $this->rawSplited = self::splitRaw($raw);
    }

    /**
     * Trim and explode the raw request input
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
        [$method, $route, $httpVersion] = explode(" ", $this->rawSplited[0]);

        $this->set("method", $method);
        $this->set("route", $route);
        $this->set("version", $httpVersion);
        $this->setFieldsAndValues();
    }

    private function setFieldsAndValues(): void
    {
        $rawSplited = array_slice($this->rawSplited, 1);
        
        foreach($rawSplited as $value) {
            [$key, $val] = explode(": ", $value);
            $this->set($key, $val);
        }
    }
    
    public function set(string $field, string $value): void
    {
        $fields = explode("-", $field);
        $fields[0] = strtolower($fields[0]);
        
        $field = implode("", $fields);

        $this->$field = $value;
    }

    public function toRaw(): void
    {
        $this->raw = implode(" ", [
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

        return $codes[$code] ? $codes[$code] : $codes["404"];
    }
}
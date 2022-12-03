<?php

namespace SparklePHP\Socket\Protocol\Http;

class Request{
    
    static private string $headerBodySeparator = PHP_EOL . PHP_EOL;

    public string $route  = "/";
    public string $method = "get";
    public Headers $headers;
    public Body $body;

    function __construct(string $raw)
    {
        [$rawHeaders, $rawBody] = explode("\n\n", $raw, 2);

        $this->headers = new Headers($rawHeaders);
        $this->body = new Body($rawBody);
    }
    
    static private function splitHeadersBody(string $raw): array
    {
        return explode(
            "\n\n",
            $raw,
            2
        );
    }

    static public function testSplitHeadersBody(string $raw): array
    {
        return self::splitHeadersBody($raw);
    }

}
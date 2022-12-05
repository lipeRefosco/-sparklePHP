<?php declare(strict_types=1);

namespace SparklePHP\Socket\Protocol\Http;

class Request{

    public Headers $headers;
    public Body $body;

    function __construct(string $raw)
    {
        $rawSplited = self::splitHeadersBody($raw);
        $rawHeaders = $rawSplited[0];
        $rawBody = $rawSplited[1];
        
        $this->headers = new Headers($rawHeaders);
        $this->body = new Body($rawBody);
    }
    
    static private function splitHeadersBody(string $rawRequest): array
    {
        $splited = explode("\n", $rawRequest);
        $headers = null; $body = null;

        $separatorIndice = 0;

        foreach($splited as $key => $value) {
            if($value === "") {
                $separatorIndice = $key;
                break;
            }
        }

        $headers = implode("\n", array_slice($splited, 0, $separatorIndice));
        $body = implode("\n", array_slice($splited, $separatorIndice, count($splited)));

        return [$headers, $body];
    }
}
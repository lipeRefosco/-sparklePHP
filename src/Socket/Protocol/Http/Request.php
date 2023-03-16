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
        $rawSplited = array_map(fn($a) => trim($a),
                                explode(PHP_EOL, trim($rawRequest))
                            );
        $isBody = false;
        $headers = "";
        $body = "";
        foreach($rawSplited as $data) {
            if($data == "") $isBody = true;

            if($isBody) $body .= $data;
            else $headers .= $data . PHP_EOL;
        }

        return [$headers, $body];
    }

    public function parseRaw(): void
    {
        $this->headers->parseRaw();
        $this->body->parseRawByContentType($this->headers->fields["Content-Type"] ?? null);
    }
}
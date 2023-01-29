<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SparklePHP\Socket\Protocol\Http\Body;
use SparklePHP\Socket\Protocol\Http\Headers;
use SparklePHP\Socket\Protocol\Http\Request;

class RequestTest extends TestCase {

    public function testIfCanSplitHeaderAndBodyFromRequest(): void
    {
        $data = <<<END
        GET / HTTP/1.1
        Host: localhost:8080
        User-Agent: insomnia/2022.4.2
        Content-Type: application/json
        Accept: */*
        Content-Length: 19
        
        {
            "key": "value"
        }
        END;

        $bodyExpect = new Body(<<<BODY
        {"key": "value"}
        BODY);
        
        $expected = [
            new Headers(<<<HEADER
            GET / HTTP/1.1
            Host: localhost:8080
            User-Agent: insomnia/2022.4.2
            Content-Type: application/json
            Accept: */*
            Content-Length: 19
            HEADER),
            $bodyExpect->raw
        ];

        $request = new Request($data);

        $result = [
            $request->headers,
            $request->body->raw
        ];

        $this->assertEquals($expected, $result);
    }

    public function testIfCanSplitHeaderAndBodyFromRequestWithBodyBadFormat(): void
    {
        $data = <<<END
        GET / HTTP/1.1
        Host: localhost:8080
        User-Agent: insomnia/2022.4.2
        Content-Type: application/json
        Accept: */*
        Content-Length: 19
        
        {
        
        
            "key": "value"
        }
        END;

        $bodyExpect = new Body("{\"key\": \"value\"}");

        $expected = [
            new Headers(<<<HEADER
            GET / HTTP/1.1
            Host: localhost:8080
            User-Agent: insomnia/2022.4.2
            Content-Type: application/json
            Accept: */*
            Content-Length: 19
            HEADER),
            $bodyExpect->raw
        ];

        $request = new Request($data);

        $result = [
            $request->headers,
            $request->body->raw
        ];

        $this->assertEquals($expected, $result);
    }

    public function testIfCanSplitHeaderAndBodyFromRequestWithMultipleLineBetweenHeaderAndBody(): void
    {
        $data = <<<END
        GET / HTTP/1.1
        Host: localhost:8080
        User-Agent: insomnia/2022.4.2
        Content-Type: application/json
        Accept: */*
        Content-Length: 19
        
        
        

        {
        
        
            "key": "value"
        }
        END;
        $bodyExpect = new Body(<<<BODY
        {"key": "value"}
        BODY);

        $expected = [
            new Headers(<<<HEADER
            GET / HTTP/1.1
            Host: localhost:8080
            User-Agent: insomnia/2022.4.2
            Content-Type: application/json
            Accept: */*
            Content-Length: 19
            HEADER),
            $bodyExpect->raw
        ];

        $request = new Request($data);

        $result = [
            $request->headers,
            $request->body->raw
        ];

        $this->assertEquals($expected, $result);
    }

    public function testIfCanSplitHeaderAndBodyFromRequestWithEmptyBody(): void
    {
        $data = <<<END
        GET / HTTP/1.1
        Host: localhost:8080
        User-Agent: insomnia/2022.4.2
        Content-Type: application/json
        Accept: */*
        Content-Length: 19

        END;
        
        $bodyExpect = new Body("");

        $expected = [
            new Headers(<<<HEADER
            GET / HTTP/1.1
            Host: localhost:8080
            User-Agent: insomnia/2022.4.2
            Content-Type: application/json
            Accept: */*
            Content-Length: 19

            HEADER),
            $bodyExpect->raw
        ];

        $request = new Request($data);
        
        $result = [
            $request->headers,
            $request->body->raw
        ];

        $this->assertEquals($expected, $result);
    }

    public function testIfCanParseRawRequest(): void
    {
        $rawHeader = <<<HEADER
                     GET / HTTP/1.1
                     Host: localhost:8080
                     User-Agent: insomnia/2022.7.3
                     Content-Type: application/json
                     Accept: */*
                     Content-Length: 21
                     HEADER;
        
        $rawBody = <<<BODY
                   {"teste": "valor"}
                   BODY;

        $fullData = <<<FULLDATA
                    $rawHeader
                    
                    $rawBody
                    FULLDATA;
        
        $headerExpected = new Headers($rawHeader);
        $headerExpected->parseRaw();

        $bodyExpect = new Body($rawBody);
        $bodyExpect->parseRawByContentType($headerExpected->fields["Content-Type"]);

        $expected = [
            "headers" => $headerExpected,
            "body" => $bodyExpect
        ];
        
        $actual = new Request($fullData);
        $actual->parseRaw();

        $this->assertEquals($expected, (array)$actual);
    }
}
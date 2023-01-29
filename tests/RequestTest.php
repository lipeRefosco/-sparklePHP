<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SparklePHP\Socket\Protocol\Http\Body;
use SparklePHP\Socket\Protocol\Http\Headers;
use SparklePHP\Socket\Protocol\Http\Request;

class RequestTest extends TestCase {

    public string $rawHeaders_with_contentType_as_json = <<<END
    GET / HTTP/1.1
    Host: localhost:8080
    User-Agent: insomnia/2022.4.2
    Content-Type: application/json
    Accept: */*
    Content-Length: 19
    END;

    public string $bodyJson_valid = '{"key": "value"}';
    public string $bodyJson_multipleSpaces = '{



        "key": "value"
    }';

    public string $bodyXML = <<<'XML'
    <?xml version="1.0" encoding="UTF-8"?>
    <note>
    <to>Tove</to>
    <from>Jani</from>
    <heading>Reminder</heading>
    <body>Don't forget me this weekend!</body>
    </note>
    XML;

    public function testIfParseRequestWithJsonInBadFormat(): void
    {
        $rawRequest = $this->rawHeaders_with_contentType_as_json .
                      PHP_EOL . PHP_EOL .
                      $this->bodyJson_multipleSpaces;

        $expected = [
            "headers" => new Headers($this->rawHeaders_with_contentType_as_json),
            "body" => new Body($this->bodyJson_valid)
        ];
        $expected["headers"]->parseRaw();
        $expected["body"]->parseRawByContentType($expected["headers"]->fields["Content-Type"]);

        $actual = new Request($rawRequest);
        $actual->parseRaw();

        $this->assertEquals($expected, (array)$actual);
    }

    public function testIfParseRequestWithMultipleLinesBetweenHeaderAndBody(): void
    {
        $rawRequest = $this->rawHeaders_with_contentType_as_json .
                      PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL .
                      $this->bodyJson_multipleSpaces;

        $expected = [
            "headers" => new Headers($this->rawHeaders_with_contentType_as_json),
            "body" => new Body($this->bodyJson_valid)
        ];
        $expected["headers"]->parseRaw();
        $expected["body"]->parseRawByContentType($expected["headers"]->fields["Content-Type"]);

        $actual = new Request($rawRequest);
        $actual->parseRaw();

        $this->assertEquals($expected, (array)$actual);
    }

    public function testIfCanParseRequestWithoutBody(): void
    {
        $data = $this->rawHeaders_with_contentType_as_json .
                PHP_EOL . PHP_EOL;

        $expected = [
            "headers" => new Headers($data),
            "body" => new Body()
        ];
        
        $expected["headers"]->parseRaw();
        $expected["body"]->parseRawByContentType($expected["headers"]->fields["Content-Type"]);

        $actual = new Request($data);
        $actual->parseRaw();

        $this->assertEquals($expected, (array)$actual);
    }

    public function testIfCanParseRequestWithAValidJson(): void
    {
        $rawRequest = $this->rawHeaders_with_contentType_as_json .
                      PHP_EOL . PHP_EOL .
                      $this->bodyJson_valid;

        $expected = [
            "headers" => new Headers($this->rawHeaders_with_contentType_as_json),
            "body" => new Body($this->bodyJson_valid)
        ];
        $expected["headers"]->parseRaw();
        $expected["body"]->parseRawByContentType($expected["headers"]->fields["Content-Type"]);

        $actual = new Request($rawRequest);
        $actual->parseRaw();

        $this->assertEquals($expected, (array)$actual);
    }

    public function testIfcanParseRequestWithBodyAsXMLAndHeaderWithContentTypeAsJson(): void
    {
        $rawRequest = $this->rawHeaders_with_contentType_as_json .
                      PHP_EOL . PHP_EOL . 
                      $this->bodyXML;
        
        $expected = [
            "headers" => new Headers($this->rawHeaders_with_contentType_as_json),
            "body" => new Body(str_replace(PHP_EOL, "", $this->bodyXML))
        ];
        $expected["headers"]->parseRaw();
        $expected["body"]->parseRawByContentType($expected["headers"]->fields["Content-Type"]);

        $actual = new Request($rawRequest);
        $actual->parseRaw();
        
        $this->assertEquals($expected, (array)$actual);
    }
}
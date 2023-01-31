<?php

use PHPUnit\Framework\ActualValueIsNotAnObjectException;
use PHPUnit\Framework\TestCase;
use SparklePHP\Socket\Protocol\Http\Body;
use SparklePHP\Socket\Protocol\Http\Headers;
use SparklePHP\Socket\Protocol\Http\Response;

class ResponseTest extends TestCase {

    public function testSendAJSON(): void
    {
        $actual = new Response();
        $actual->setup();
        $actual->sendJSON([
            "AnyKey" => "any Value"
        ]);
        $actual->toRaw();

        $expected = [
            "raw" => <<<RESPONSE
            HTTP/1.1 200 OK
            Content-Type: application/json

            {"AnyKey":"any Value"}
            RESPONSE,
            "headers" => new Headers(),
            "body" => new Body('{"AnyKey":"any Value"}'),
            "separator" => PHP_EOL
        ];

        $expected["headers"]->setStatus("200");
        $expected["headers"]->setVersion("HTTP/1.1");
        $expected["headers"]->set("Content-Type", "application/json");
        $expected["headers"]->toRaw();
        $expected["body"]->set("raw", json_encode(["AnyKey" => "any Value"]));

        $this->assertEquals($expected, (array)$actual);
    }

    public function testSendHTML(): void
    {
        $data = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
        </head>
        <body>
            
        </body>
        </html>
        HTML;
        
        $actual = new Response();
        $actual->setup();
        $actual->sendHTML($data);
        $actual->toRaw();

        $expected = [
            "raw" => <<<RAWRESPONSE
            HTTP/1.1 200 OK
            Content-Type: text/html; charset=UTF-8

            $data
            RAWRESPONSE,
            "headers" => new Headers(),
            "body" => new Body(),
            "separator" => PHP_EOL
        ];
        $expected["headers"]->setVersion("HTTP/1.1");
        $expected["headers"]->setStatus("200");
        $expected["headers"]->set("Content-Type", "text/html; charset=UTF-8");
        $expected["headers"]->toRaw();
        $expected["body"]->set("raw", $data);

        $this->assertEquals($expected, (array)$actual);
    }

    public function testSetupResponse(): void
    {
        $actual = new Response();
        $actual->setup();

        $expected = [
            "headers" => new Headers(),
            "body" => new Body(),
            "separator" => PHP_EOL
        ];
        $expected["headers"]->setStatus("200");
        $expected["headers"]->setVersion("HTTP/1.1");

        $this->assertEqualsCanonicalizing($expected, (array)$actual);
    }
}
<?php

use PHPUnit\Framework\TestCase;
use SparklePHP\Socket\Protocol\Http\Body;

class BodyTest extends TestCase {

    public function testBasicInitialization(): void
    {
        $expected = [
            "raw"  => "",
            "data" => ""
        ];

        $actual = (array)new Body("");
        
        $this->assertEquals($expected, $actual);
    }

    public function testWithDataToBeParsedToContentType(): void
    {
        $actual = new Body('{"data": "value"}');
        $actual->set("contentType", "application/json");
        $actual->parseRawByContentType();

        $expected = [
            "raw"  => '{"data": "value"}',
            "contentType" => "application/json",
            "data" => [
                "data" => "value"
            ]
        ];
        
        $this->assertEquals($expected, (array)$actual);
    }

    public function testTryParseApplicationJsonToRaw(): void
    {
        $expected = [
            "raw"  => '{"key":"value"}',
            "contentType" => "application/json",
            "data" => [
                "key" => "value"
            ]
        ];

        $actual = new Body("");
        $actual->set("contentType", "application/json");
        $actual->set("data", [
            "key" => "value"
        ]);
        $actual->toRaw();

        $this->assertEquals($expected, (array)$actual);
    }

    public function testTryParseHTMLToRAW(): void
    {

        $data = <<<END
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
        END;

        $expected = [
            "raw" => $data,
            "contentType" => "text/html",
            "data" => $data
        ];

        $actual = new Body();
        $actual->set("contentType", "text/html");
        $actual->set("data", $data);
        $actual->toRaw();

        $this->assertEquals($expected, (array)$actual);
    }
}
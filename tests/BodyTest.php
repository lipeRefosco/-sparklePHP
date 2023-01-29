<?php

use PHPUnit\Framework\TestCase;
use SparklePHP\Socket\Protocol\Http\Body;

class BodyTest extends TestCase {

    private string $basicHTML = <<<END
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

    public function testBasicInitialization(): void
    {
        $expected = [
            "raw" => null
        ];

        $actual = (array)new Body();
        
        $this->assertEquals($expected, $actual);
    }

    public function testWithDataToBeParsedToContentType(): void
    {
        $contentTypeFromHeader = "application/json";

        $actual = new Body('{"data": "value"}');
        $actual->parseRawByContentType($contentTypeFromHeader);

        $expected = [
            "raw"  => '{"data": "value"}',
            "data" => [
                "data" => "value"
            ]
        ];
        
        $this->assertEquals($expected, (array)$actual);
    }

    public function testTryParseRawDataToContentTypeWhenContentTypeIsNull(): void
    {
        $data = $this->basicHTML;

        $contentTypeFromHeader = null;

        $actual = new Body($data);
        $actual->parseRawByContentType($contentTypeFromHeader);

        $expected = [
            "raw" => $data,
            "data" => $data
        ];

        $this->assertEquals($expected, (array)$actual);
    }

    public function testTryParseApplicationJsonToRaw(): void
    {
        $expected = [
            "raw"  => '{"key":"value"}',
            "data" => [
                "key" => "value"
            ]
        ];

        $contentTypeFromHeader = "application/json";

        $actual = new Body("");
        $actual->set("data", [
            "key" => "value"
        ]);
        $actual->toRaw($contentTypeFromHeader);

        $this->assertEquals($expected, (array)$actual);
    }

    public function testTryParseHTMLToRAW(): void
    {

        $data = $this->basicHTML;

        $expected = [
            "raw" => $data,
            "data" => $data
        ];

        $contentTypeFromHeader = "text/html";

        $actual = new Body();
        $actual->set("data", $data);
        $actual->toRaw($contentTypeFromHeader);

        $this->assertEquals($expected, (array)$actual);
    }
}
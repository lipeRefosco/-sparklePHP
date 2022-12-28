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

    public function testWithData(): void
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
}
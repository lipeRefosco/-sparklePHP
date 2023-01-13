<?php

use PHPUnit\Framework\TestCase;
use SparklePHP\Socket\Protocol\Http\Body;
use SparklePHP\Socket\Protocol\Http\Headers;
use SparklePHP\Socket\Protocol\Http\Response;
use SparklePHP\Socket\Protocol\Http\ResponseStatusException;

class ResponseTest extends TestCase {

    public function testTryCreateResposeSendingAJSONData(): void
    {
        $resposeTest = new Response();
        $resposeTest->setDefault();
        $resposeTest->send([
            "AnyKey" => "any Value"
        ]);
        $resposeTest->toRaw();

        $expectedRes = [
            "raw" => <<<END
                     HTTP/1.1 200 OK
                     
                     {"AnyKey":"any Value"}
                     END,
            "headers" => new Headers(),
            "body" => new Body('{"AnyKey":"any Value"}'),
            "separator" => PHP_EOL
        ];
        $expectedRes["headers"]->setStatus("200");
        $expectedRes["headers"]->setVersion("HTTP/1.1");
        $expectedRes["headers"]->set("content-Type", "text/html; charset=UTF-8");
        $expectedRes["headers"]->toRaw();
        $expectedRes["body"]->set("data", ["AnyKey" => "any Value"]);

        $this->assertEquals($expectedRes, (array)$resposeTest);
    }

    public function testSetDefault(): void
    {
        $defaultRespose = new Response();
        $defaultRespose->setDefault();

        $responseHeadersExpectedFileds = [
            "content-type" => "text/html; charset=UTF-8"
        ];
        $this->assertEquals(
            $responseHeadersExpectedFileds,
            $defaultRespose->headers->fields
        );
    }
}
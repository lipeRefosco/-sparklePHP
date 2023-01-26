<?php

use PHPUnit\Framework\TestCase;
use SparklePHP\Socket\Protocol\Http\Headers;

class HeadersTest extends TestCase
{
    public function testHeaderParserMethod():void
    {
        $raw = <<<END
        GET /favicon.ico HTTP/1.1
        Host: localhost:8080
        User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:83.0) Gecko/20100101 Firefox/83.0
        Accept: image/avif,image/webp,*/*
        Accept-Language: en-US,en;q=0.5
        Accept-Encoding: gzip, deflate, br
        END;

        $rawSplited = explode("\n", $raw);

        $expected = [
            "raw"        => $raw,
            "rawSplited" => $rawSplited,
            "method"     => "GET",
            "route"      => "/favicon.ico",
            "version"    => "HTTP/1.1",
            "fields"     => [
                "Host"            => "localhost:8080",
                "User-Agent"      => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:83.0) Gecko/20100101 Firefox/83.0",
                "Accept"          => "image/avif,image/webp,*/*",
                "Accept-Language" => "en-US,en;q=0.5",
                "Accept-Encoding" => "gzip, deflate, br"
            ]
        ];

        $result = new Headers($raw);
        $result->parseRaw();

        $this->assertEquals($expected, (array)$result);
    }

    public function testHeaderParserMethodWithQueryParams(): void
    {
        $data = <<<END
                GET /?teste=4 HTTP/1.1
                Host: localhost:8080
                END;

        $dataSplited = explode("\n", $data);

        $actual = new Headers($data);
        $actual->parseRaw();

        $expected = [
            "raw"        => $data,
            "rawSplited" => $dataSplited,
            "method"     => "GET",
            "route"      => "/",
            "query"      => [
                "teste" => 4
            ],
            "version"    => "HTTP/1.1",
            "fields"     => [ "Host"  => "localhost:8080" ]
        ];

        $this->assertEquals($expected, (array)$actual);
    }

    public function testToRawMethod(): void
    {
        $result = new Headers();
        $result->setStatus("200");
        $result->setVersion("HTTP/1.1");
        $result->set("Content-Type", "application/json");
        $result->toRaw();

        $expected = [
            "raw" => <<<END
                     HTTP/1.1 200 OK
                     Content-Type: application/json
                     
                     END,
            "status" => "200",
            "version" => "HTTP/1.1",
            "fields" => [
                "Content-Type" => "application/json"
            ]
        ];

        $this->assertEquals($expected, (array)$result);
    }
}
<?php

use PHPUnit\Framework\TestCase;
use SparklePHP\Socket\Protocol\Http\Headers;

class HeadersTest extends TestCase
{
    public function testHeaderClass():void
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

    public function testTheToRawMethod(): void
    {
        $result = new Headers();
        $result->set("Status", "200");
        $result->set("Version", "HTTP/1.1");
        $result->set("Content-Type", "application/json");
        $result->toRaw();

        $expected = [
            "raw" => <<<END
                     HTTP/1.1 200 OK
                     Content-Type: application/json
                     END,
            "rawSplited" => [],
            "status" => "200",
            "version" => "HTTP/1.1",
            "fields" => [
                "contentType" => "application/json"
            ], 
        ];

        $this->assertEquals($expected, (array)$result);
    }
}
<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SparklePHP\Socket\Protocol\Http\Headers;

final class HeadersTest extends TestCase
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

        $expected = (Array)[
            "raw"             => $raw,
            "rawSplited"      => [
                "GET /favicon.ico HTTP/1.1",
                "Host: localhost:8080",
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:83.0) Gecko/20100101 Firefox/83.0",
                "Accept: image/avif,image/webp,*/*",
                "Accept-Language: en-US,en;q=0.5",
                "Accept-Encoding: gzip, deflate, br"
            ],
            "method"          => "GET",
            "route"           => "/favicon.ico",
            "version"         => "HTTP/1.1",
            "host"            => "localhost:8080",
            "userAgent"      => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:83.0) Gecko/20100101 Firefox/83.0",
            "accept"          => "image/avif,image/webp,*/*",
            "acceptLanguage" => "en-US,en;q=0.5",
            "acceptEncoding" => "gzip, deflate, br"
        ];

        $result = new Headers($raw);
        $result->parseRaw();

        $this->assertEquals($expected, (array)$result);
    }
}
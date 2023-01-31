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

        $actual = new Headers($raw);
        $actual->parseRaw();
        
        $this->assertEquals($expected, (array)$actual);
    }

    public function testHeaderParserMethodWithQueryParams(): void
    {
        $data = <<<END
                GET /?bar=4&foo=24 HTTP/1.1
                Host: localhost:8080
                END;

        $actual = new Headers($data);
        $actual->parseRaw();

        $expected = [
            "method"     => "GET",
            "route"      => "/",
            "query"      => [
                "bar" => "4",
                "foo"   => "24"
            ],
            "version"    => "HTTP/1.1",
            "fields"     => [ "Host"  => "localhost:8080" ]
        ];

        $this->assertEquals($expected, (array)$actual);
    }

    public function testToRawMethod(): void
    {
        $actual = new Headers();
        $actual->setStatus("200");
        $actual->setVersion("HTTP/1.1");
        $actual->set("Content-Type", "application/json");
        $actual->toRaw();

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

        $this->assertEquals($expected, (array)$actual);
    }

    public function testParseWhenQueryParamsHasAKeyWithoutValue(): void
    {
        $rawHeader = <<<HEADER
        GET /?bar=4&foo HTTP/1.1
        Host: localhost:8080
        HEADER;

        $actual = new Headers($rawHeader);
        $actual->parseRaw();

        $expected = [
            "method"     => "GET",
            "route"      => "/",
            "query"      => [
                "bar" => "4",
                "foo"   => ""
            ],
            "version"    => "HTTP/1.1",
            "fields"     => [ "Host"  => "localhost:8080" ]
        ];

        $this->assertEquals($expected, (array)$actual);
    }

    public function testParseWhenQueryParamsHasAKeyWithEmptyValue(): void
    {
        $rawHeader = <<<HEADER
        GET /?bar=4&foo= HTTP/1.1
        Host: localhost:8080
        HEADER;

        $actual = new Headers($rawHeader);
        $actual->parseRaw();

        $expected = [
            "method"     => "GET",
            "route"      => "/",
            "query"      => [
                "bar" => "4",
                "foo"   => ""
            ],
            "version"    => "HTTP/1.1",
            "fields"     => [ "Host"  => "localhost:8080" ]
        ];

        $this->assertEquals($expected, (array)$actual);
    }
}
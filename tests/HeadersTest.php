<?php declare(strict_types=1);

use PhpParser\Node\Expr\Cast\Object_;
use PHPUnit\Framework\TestCase;
use SparklePHP\Socket\Protocol\Http\Headers;

final class HeadersTest extends TestCase
{
    public function testCanExtractMethodRouteAndHttpVersionFromRequestHeader(): void
    {
        $data = <<<END
        GET /favicon.ico HTTP/1.1
        Host: localhost:8080
        User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:83.0) Gecko/20100101 Firefox/83.0
        Accept: image/avif,image/webp,*/*
        Accept-Language: en-US,en;q=0.5
        Accept-Encoding: gzip, deflate, br
        END;

        $expected = [
                "GET",
                "/favicon.ico",
                "HTTP/1.1",
                [
                    "Host" => "localhost:8080",
                    "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:83.0) Gecko/20100101 Firefox/83.0",
                    "Accept" => "image/avif,image/webp,*/*",
                    "Accept-Language" => "en-US,en;q=0.5",
                    "Accept-Encoding" => "gzip, deflate, br",
                ]
            ];

        $result = Headers::parseHeaders($data);

        $this->assertEquals(
            $expected,
            $result
        );
    }

    public function testHeaderClass():void
    {
        $expected = (Array)[
            "method"          => "get",
            "route"           => "/favicon.ico",
            "version"         => "http/1.1",
            "host"            => "localhost:8080",
            "user-agent"      => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:83.0) Gecko/20100101 Firefox/83.0",
            "accept"          => "image/avif,image/webp,*/*",
            "accept-language" => "en-US,en;q=0.5",
            "accept-encoding" => "gzip, deflate, br"
        ];

        $result = (Array)new Headers(<<<END
        GET /favicon.ico HTTP/1.1
        Host: localhost:8080
        User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:83.0) Gecko/20100101 Firefox/83.0
        Accept: image/avif,image/webp,*/*
        Accept-Language: en-US,en;q=0.5
        Accept-Encoding: gzip, deflate, br
        END);

        $this->assertEquals($expected, $result);
    }
}
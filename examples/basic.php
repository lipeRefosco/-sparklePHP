<?php

require_once "./vendor/autoload.php";

use SparklePHP\Socket\Protocol\Http\Request;
use SparklePHP\Socket\Protocol\Http\Response;
use SparklePHP\Sparkle;

$address = 'localhost';
$port = 8080;

$app = new Sparkle($address, $port);

$app->get("/", function (Request $req, Response $res) {
    $res->send((array)$req);
});

$app->listen(function (Sparkle $app) {
    echo "http://{$app->getAddress()}:{$app->getPort()}" . PHP_EOL;
});
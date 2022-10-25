<?php

// require "./Sparkle.php";

require_once "./vendor/autoload.php";

use SparklePHP\Socket\Protocol\Http;

$app = new Http();
$app->addres = 'localhost';
$app->port = 8080;

$app->get("/", function () {
    return;
});

$app->listen(function ($obj) {
    echo "Listen on http://$obj->addres:$obj->port" . PHP_EOL;
});
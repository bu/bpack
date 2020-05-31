<?php

// load everything
require __DIR__ . "/vendor/autoload.php";

//
$app = new bPack\Foundation;

// load feature we need
$app->load(new bPack\Packages\Routing);
$app->load(new bPack\Database);

//
$dest = $app->router->route(
    $app->router->purifyMethod($_SERVER["REQUEST_METHOD"]),
    $app->router->purifyURI($_SERVER["REQUEST_URI"])
);

// global wrapper in case any error occured
try {
    $app->dispatcher->dispatch($dest)->send();
    $app->terminate();
} catch (\Exception $e) {
    $app->isDevMode() && var_dump($e);
}

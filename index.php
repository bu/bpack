<?php

// load everything
require __DIR__ . "/vendor/autoload.php";

$app = new bPack\Foundation([
    // app
    "timezone" => "Asia/Taipei",
    "devMode" => true,
    "rootDir" => __DIR__,

    // router
    "router.routesFile" => __DIR__ . "/config/routes.php",
    "router.autoloadRoutes" => true,
]);

//
$dest = $app->router->route(
    $app->router->purifyMethod($_SERVER["REQUEST_METHOD"]),
    $app->router->purifyURI($_SERVER["REQUEST_URI"])
);

// global wrapper in case any error occured
try {
    $response = $app->dispatcher->dispatch($dest);
    $response->send();

    $app->terminate();
} catch (\Exception $e) {
    $app->isDevMode() && var_dump($e);
}

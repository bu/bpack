<?php
// load everything
require __DIR__ . "/vendor/autoload.php";

$app = new bPack\Foundation([
    "timezone" => "Asia/Taipei",
    "devMode" => true,
]);

try {
    // $app->router->dispatch();
    throw new \Exception("test");
} catch (\Exception $e) {
    // only dump exception when dev mode is enabled
    $app->isDevMode() && var_dump($e);
}

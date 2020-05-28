<?php

namespace bPack;

class Foundation
{
    public Protocol\Router $router;
    public Protocol\Config $config;
    public Protocol\Dispatcher $dispatcher;

    public function __construct($options)
    {
        $this->config = new Config($this);
        $this->router = new Router($this);
        $this->dispatcher = new Dispatcher($this);

        $this->config->batch(array_merge(
            (array) new FoundationDefaultConfig,
            $options
        ));
    }

    public function isDevMode(): bool
    {
        return (bool) $this->config->get("devMode", false);
    }

    public function terminate():void {
        if (isset($_SERVER) && isset($_SERVER["GATEWAY_INTERFACE"])) {
            fastcgi_finish_request();
        }

        exit;
    }
}

final class FoundationDefaultConfig {
    public string $timezone = "UTC";
    public bool $devMode = false;
    public string $rootDir = __DIR__ . "/../";
}

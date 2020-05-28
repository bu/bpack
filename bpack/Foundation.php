<?php

namespace bPack;

class Foundation
{
    public Protocol\Config $config;

    public function __construct($options)
    {
        $this->config = new Config($this);

        $this->config->batch(array_merge(
            (array) new FoundationDefaultConfig,
            $options
        ));
    }

    public function isDevMode(): bool
    {
        return (bool) $this->config->get("devMode", false);
    }

    }

final class FoundationDefaultConfig {
    public string $timezone = "UTC";
    public bool $devMode = false;
    public string $rootDir = __DIR__ . "/../";
}

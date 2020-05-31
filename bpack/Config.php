<?php declare(strict_types=1);
namespace bPack;

class Config implements Protocol\Config {
    protected $configs = [];

    public function get(string $key, $default_value) {
        return $this->configs[$key] ?? $default_value;
    }

    public function getPath(string $key, $default_value) {
        return $this->configs[$key] ?? str_replace("{{ rootDir }}", $this->get("app.rootDir", __DIR__ . "/../"), $default_value);
    }

    public function set(string $key, $value): bool {
        $this->configs[$key] = $value;

        return true;
    }
    public function batch($configs): bool {
        $this->configs = array_merge($this->configs, $configs);
        return true;
    }
}

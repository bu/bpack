<?php declare(strict_types=1);
namespace bPack\Protocol;

interface Config {
    public function get(string $key, $default_value);
    public function getPath(string $key, $default_value);

    public function set(string $key, $value): bool;
    public function batch($configs): bool;
}

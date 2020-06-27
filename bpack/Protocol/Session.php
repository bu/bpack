<?php declare(strict_types=1);
namespace bPack\Protocol;

interface Session {
    public function __construct(SessionStorage $storage);
    public function getSessionName(): string;

    public function start(Request $req, Response $res):Session;

    public function set(string $key, $value):Session;
    public function get(string $key, $default_value = null);

    public function delete(string $key):bool;

    public function flush(string $key);
    public function getSessionId(bool $force = false):string;
}

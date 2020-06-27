<?php declare(strict_types=1);

namespace bPack\Session;

use \bPack;

class FileStorage implements bPack\Protocol\SessionStorage {
    public function __construct() {
        $this->app = bPack\Foundation::getInstance();
        $this->app->config->required(["SESSION_FILE_STORAGE_PATH"]);
    }

    protected function buildFilepath(string $sessionId):string {
        return $this->app->rootpath($_ENV["SESSION_FILE_STORAGE_PATH"] . "/" . $sessionId . ".json");
    }

    public function read(string $sessionId):array {
        $sessionFilepath = $this->buildFilepath($sessionId);

        if(!file_exists($sessionFilepath)) {
            return [];
        }

        $sessionFileContent = file_get_contents($sessionFilepath);
        return (array) json_decode($sessionFileContent, true);
    }

    public function write(string $sessionId, array $data):bool {
        return file_put_contents(
            $this->buildFilepath($sessionId),
            json_encode($data, JSON_UNESCAPED_UNICODE)
        ) !== false;
    }

    public function destroy(string $sessionId):bool {
        $sessionFilepath = $this->buildFilepath($sessionId);

        if(!file_exists($sessionFilepath)) {
            return true;
        }

        return unlink($sessionFilepath);
    }
}

<?php declare(strict_types=1);
namespace bPack;

use \PDO;

class Database implements Protocol\Module {
    // Foundation
    protected $app;
    // \PDO
    protected $db;

    public function getIdentitifer():string {
        return "db";
    }

    public function setApplication(Foundation $app): void {
        $this->app = $app;
        $app->config->required(["DB_DSN", "DB_USER", "DB_PASSWORD"]);
    }

    public function getConnection():PDO {
        if(isset($this->db)) {
            return $this->db;
        }

        $this->db = new PDO($_ENV["DB_DSN"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $this->db;
    }
}

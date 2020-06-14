<?php declare(strict_types=1);
namespace bPack\ORM;

use \bPack\Protocol;
use \bPack;

abstract class Model implements Protocol\Model {
    protected bPack\Foundation $app;
    protected bPack\Database $db;

    public function __construct(bPack\Foundation $app) {
        $this->app = $app;
        $this->db = $app->db;
    }

    public function getConnection():\PDO {
        return $this->db->getConnection();
    }

    public function getSchemaName():string {
        return get_class($this);
    }

    // creation process
    public function create(array $newData):bool {
        $entity = new ModelEntity($this, $data);
        return $entity->save();
    }

    public function new():Protocol\ModelEntity {
        return new ModelEntity($this);
    }

    // query
    public function all():Protocol\ModelCollection {
        return new ModelCollection($this, []);
    }

    public function find_by(string $field, $value):Protocol\ModelCollection{
        return new ModelCollection($this, [
            [$field, "=", $value]
        ]);
    }

    public function find_by_where(array $conditions):Protocol\ModelCollection {
        return new ModelCollection($this, $conditions);
    }
}

<?php declare(strict_types=1);

namespace bPack\Protocol;

use \PDO;

interface Model {
    // get database connection
    public function getConnection():PDO;
    
    // get model info
    public function getSchema():array;
    public function getSchemaName():string;
    public function getTablename():string;

    // creation
    public function create(array $newData):bool;
    public function new():ModelEntity;

    // query
    public function all():ModelCollection;
    public function find_by(string $field, $value):ModelCollection;
    public function find_by_where(array $conditions):ModelCollection;
}

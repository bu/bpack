<?php declare(strict_types=1);

namespace bPack\Protocol;

interface Model {
    public function create(array $newData):bool;
    public function new():ModelEntity;

    // query
    public function all():ModelCollection;
    public function find_by(string $field, $value):ModelCollection;
    public function find_by_where(array $conditions):ModelCollection;
}

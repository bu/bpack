<?php declare(strict_types=1);

namespace bPack\Protocol;

interface ModelCollection {
    public function first():?ModelEntity;
    
    public function destroy():bool;
    public function update(array $updatedData):bool;

    public function limit(int $limitCount):ModelCollection;
    public function offest(int $offsetValue):ModelCollection;
    public function orderBy(array $orderByExpression):ModelCollection;
    public function select(string ...$columns):ModelCollection;

    public function pluck(string $column):array;
}

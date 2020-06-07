<?php declare(strict_types=1);

namespace bPack\Protocol;

interface ModelCollection {
    public function first():?ModelEntity;
    public function last():?ModelEntity;

    public function firstN(int $count = 1):array;
    public function lastN(int $count = 1):array;

    public function all():array;
    public function destroy():bool;
    public function update(array $updatedData):bool;

    public function limit(int $limitCount):ModelCollection;
    public function offest(int $offsetValue):ModelCollection;

    public function orderBy():ModelCollection;
    public function requestPlainObject():ModelCollection;

    public function pluck(string $column):array;
}

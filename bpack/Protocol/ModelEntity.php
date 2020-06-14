<?php declare(strict_types=1);

namespace bPack\Protocol;

interface ModelEntity {
    public function save():bool;
    public function update(array $updatedData):bool;
    public function destroy():bool;
    public function unwrap():array;
}

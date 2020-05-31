<?php declare(strict_types=1);
namespace bPack\Protocol;

interface Module {
    public function getIdentitifer(): string;
    public function setApplication(\bPack\Foundation $app): void;
}

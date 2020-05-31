<?php declare(strict_types=1);
namespace bPack;

class Database implements Protocol\Module {
    protected Foundation $app;

    public function getIdentitifer():string {
        return "database";
    }

    public function setApplication(Foundation $app): void {
        $this->app = $app;
    }
}

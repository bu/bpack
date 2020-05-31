<?php declare(strict_types=1);
namespace bPack\Packages;

use \bPack;

class Routing implements bPack\Protocol\Module {
    protected Foundation $app;

    public function getIdentitifer():string {
        return "routing";
    }

    public function setApplication(bPack\Foundation $app): void {
        $app->load(new bPack\Router);
        $app->load(new bPack\Dispatcher);
        $app->unload($this);
    }
}

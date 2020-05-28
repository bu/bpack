<?php
namespace bPack;

class Controller implements Protocol\Controller {
    protected Foundation $app;

    public function __construct(Foundation $app) {
        $this->app = $app;
    }
}

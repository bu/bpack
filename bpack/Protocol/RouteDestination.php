<?php declare(strict_types=1);
namespace bPack\Protocol;

class RouteDestination {
    // array
    public $handler;
    // array
    public $params;

    public function __construct(array $route) {
        $this->handler = $route['handler'];
        $this->params = $route['params'];
    }
}

<?php
namespace bPack\Protocol;

class RouteDestination {
    public array $handler;
    public array $params;

    public function __construct(array $route) {
        $this->handler = $route['handler'];
        $this->params = $route['params'];
    }
}

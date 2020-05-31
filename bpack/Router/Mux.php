<?php declare(strict_types=1);
namespace bPack\Router;

use \FastRoute;

class Mux {
    protected FastRoute\RouteCollector $fastroute;
    protected array $currentRouteMiddleware = [];

    public function __construct(FastRoute\RouteCollector &$r) {
        $this->fastroute = $r;
    }

    public function get(string $path,  ...$handlers):void {
        // attach previous stated middleware to front of this route
        $handlers = array_merge($this->currentRouteMiddleware, $handlers);
        // add to route map
        $this->fastroute->addRoute("GET", $path, $handlers);
    }

    public function post(string $path,  ...$handlers):void {
        // attach previous stated middleware to front of this route
        $handlers = array_merge($this->currentRouteMiddleware, $handlers);
        // add to route map
        $this->fastroute->addRoute("POST", $path, $handlers);
    }

    public function use( ...$handlers):void {
        // append to the end of middleware chains
        $this->currentRouteMiddleware = array_merge($this->currentRouteMiddleware, $handlers);
    }

    // for those method we not support yet
    public function __call(string $method, $arguments):void {
        throw new \Exception("Given method {$method} is not supported in current Mux.");
    }
}

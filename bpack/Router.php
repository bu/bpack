<?php declare(strict_types=1);
namespace bPack;

use \FastRoute;

class Router implements Protocol\Router, Protocol\Module {
    protected Foundation $app;

    protected FastRoute\RouteCollector $routes;
    protected FastRoute\Dispatcher\GroupCountBased $dispatcher;

    const NOT_FOUND = FastRoute\Dispatcher::NOT_FOUND;
    const METHOD_NOT_ALLOWED = FastRoute\Dispatcher::METHOD_NOT_ALLOWED;
    const FOUND = FastRoute\Dispatcher::FOUND;

    const DefaultHandlerController = "\bPack\Controller\DefaultHandler";

    // as module
    public function getIdentitifer():string {
        return "router";
    }

    public function setApplication(Foundation $app): void {
        $this->app = $app;

        $shouldAutoload = $_ENV["ROUTER_AUTOLOAD"] ?? true;
        $shouldAutoload && $this->loadRoutes();

        $app->config->required(["ROUTER_ROUTES"]);
    }

    //
    public function __construct() {
        $this->routes = new FastRoute\RouteCollector(
            new FastRoute\RouteParser\Std,
            new FastRoute\DataGenerator\GroupCountBased
        );
    }

    public function loadRoutes() {
        $route_file = $this->app->rootpath($_ENV["ROUTER_ROUTES"] ?? "config/routes.php");

        //
        $mux = new Router\Mux($this->routes);
        $routeFunc = require $route_file;
        $routeFunc($mux);

        $this->dispatcher = new FastRoute\Dispatcher\GroupCountBased($this->routes->getData());
    }

    public function route(string $method, string $uri):Protocol\RouteDestination {
        $dispatchResult = $this->dispatcher->dispatch($method, $uri);

        $handler = "";
        $params = [];

        switch($dispatchResult[0]) {
            case Router::NOT_FOUND:
                $handler = [
                    $_ENV["ROUTER_NOTFOUND_HANDLER"] ?? self::DefaultHandlerController . "#notFound"
                ];
            break;

            case Router::METHOD_NOT_ALLOWED:
                $handler = [
                    $_ENV["ROUTER_NOTALLOWED_HANDLER"] ?? self::DefaultHandlerController . "#methodNotAllowed"
                ];

                $params = [
                    "allowedMethods" => $dispatchResult[1]
                ];
            break;

            case Router::FOUND:
                $handler = $dispatchResult[1];
                $params = $dispatchResult[2];
            break;
        }

        return new Protocol\RouteDestination([
            "handler" => $handler,
            "params" => $params
        ]);
    }

    public static function purifyMethod(string $method):string {
        return strtoupper($method);
    }

    public static function purifyURI(string $uri):string {
        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);

        return $uri;
    }
}

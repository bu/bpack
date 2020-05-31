<?php declare(strict_types=1);
namespace bPack\Protocol;

interface Dispatcher {
    public function dispatch(RouteDestination $route):Response;
}

<?php
namespace bPack\Protocol;

interface Dispatcher {
    public function dispatch(RouteDestination $route):Response;
}

<?php declare(strict_types=1);
namespace bPack\Protocol;

interface Router {
    public function route(string $method, string $url):RouteDestination;
}

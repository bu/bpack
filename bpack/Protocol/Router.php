<?php
namespace bPack\Protocol;

interface Router {
    public function route(string $method, string $url):RouteDestination;
}

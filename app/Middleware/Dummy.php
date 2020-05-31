<?php declare(strict_types=1);
namespace App\Middleware;

use \bPack\Protocol\Middleware as MiddlewareInterface;
use \bPack\Protocol\Request as RequestInterface;
use \bPack\Protocol\Response as ResponseInterface;
use \bPack\Protocol\Pipeline as HandlerInterface;

class Dummy implements MiddlewareInterface {
    public function process(RequestInterface $req, HandlerInterface $handler):ResponseInterface {
        $response = $handler->handle($req);
        return $response;
    }
}

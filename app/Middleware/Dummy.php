<?php
namespace App\Middleware;

use \bPack\Protocol\Middleware as MiddlewareInterface;
use \bPack\Protocol\Request as RequestInterface;
use \bPack\Protocol\Response as ResponseInterface;

class Dummy implements MiddlewareInterface {
    public function process(RequestInterface $req, ResponseInterface $res) {

    }
}

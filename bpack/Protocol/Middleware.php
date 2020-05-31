<?php declare(strict_types=1);
namespace bPack\Protocol;

interface Middleware {
    public function process(Request $request, Pipeline $handler): Response;
}

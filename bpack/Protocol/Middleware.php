<?php
namespace bPack\Protocol;

interface Middleware {
    public function process(Request $request, Pipeline $handler): Response;
}

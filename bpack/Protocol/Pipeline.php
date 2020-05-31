<?php declare(strict_types=1);
namespace bPack\Protocol;

interface Pipeline {
    public function __construct(array $items, Middleware $fallback);
    public function handle(Request $req): Response;
}

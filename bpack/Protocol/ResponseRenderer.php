<?php declare(strict_types=1);
namespace bPack\Protocol;

interface ResponseRenderer {
    public function getContentType():string;
    public function render();
}

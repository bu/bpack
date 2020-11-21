<?php
namespace bPack\Response;

use \bPack;

class Text implements bPack\Protocol\ResponseRenderer {
    // string
    private $data;

    public function __construct(string $data) {
        $this->data = $data;
    }

    public function getContentType():string {
        return "text/plain";
    }

    public function render():string {
        return $this->data;
    }
}

<?php
namespace bPack\Response;

use \bPack;

class HTML implements bPack\Protocol\ResponseRenderer {
    private string $data;

    public function __construct(string $data) {
        $this->data = $data;
    }

    public function getContentType():string {
        return "text/html";
    }

    public function render():string {
        return $this->data;
    }

    public function update(string $newData):void {
        $this->data = $newData;
    }
}

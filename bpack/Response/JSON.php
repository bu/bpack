<?php
namespace bPack\Response;

use \bPack;


class JSON implements bPack\Protocol\ResponseRenderer {
    private array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function getContentType():string {
        return "application/json";
    }

    public function render():string {
        return json_encode($this->data, JSON_UNESCAPED_UNICODE);
    }
}

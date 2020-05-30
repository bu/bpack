<?php
namespace bPack\Protocol;

interface ResponseRenderer {
    public function getContentType():string;
    public function render():string;
}

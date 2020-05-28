<?php
namespace bPack;

class Response implements Protocol\Response {
    public function send():void {
        echo "123";
    }
}

<?php declare(strict_types=1);
namespace App;

class Index extends Base {
    public function index() {
        $this->res->html("<h1>hello world</h1>");
        $this->res->update("<h1>hello world122121</h1>");
    }

    public function hello($name, $name2) {
        $this->res->json([
            "message" => "hello world1 to {$name} and {$name2}",
        ]);

        $this->res->json([
            "message" => "hello world2 to {$name} and {$name2}",
        ]);
    }
}

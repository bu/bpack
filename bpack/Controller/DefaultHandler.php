<?php declare(strict_types=1);
namespace bPack\Controller;

use \bPack;

class DefaultHandler extends bPack\Controller {

    public function notFound() {
        $this->res->status(404)->json([
            "status" => 404,
            "msg" => "Requested URI is not found.",
        ]);

        return;
    }

    public function methodNotAllowed(array $allowedMethods) {
        $this->res->status(405)->json([
            "status" => 405,
            "msg" => "Method not allowed, only allows " . implode(",", $allowedMethods),
        ]);

        return;
    }

    public function caughtException(\Exception $exception) {
        $this->foundation->isDevMode() && var_dump($e);
    }

}

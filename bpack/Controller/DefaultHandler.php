<?php
namespace bPack\Controller;

class DefaultHandleController extends Controller {

    public function notFound() {

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

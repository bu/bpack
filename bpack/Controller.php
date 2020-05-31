<?php declare(strict_types=1);
namespace bPack;

class Controller implements Protocol\Controller {
    protected Foundation $app;
    protected bool $initialized = false;

    protected Protocol\Request $req;
    protected Protocol\Response $res;

    public function __construct(Foundation $app) {
        $this->app = $app;
    }

    public function __init(Protocol\Request $req, Protocol\Response $res):void {
        $this->req = $req;
        $this->res = $res;
        $this->initialized = true;
    }

    public function __exec(string $methodName, array $params) {
        method_exists($this, "beforePage") && $this->beforePage();
        call_user_func_array([$this, $methodName], $params);
        method_exists($this, "afterPage") && $this->afterPage();
    }
}

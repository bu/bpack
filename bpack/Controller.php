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

    // magic calls
    public function __get(string $var_name) {
        // if first character is not uppercase, then we should throw exception
        if($var_name[0] != strtoupper($var_name[0])) {
            throw new \Exception("[Controller] request access to an undefined property on controller class.");
        }

        // build class name
        $className = "\App\\Model\\{$var_name}";

        // if uppercase, then we firstly think this is a Model class
        try {
            $this->{$var_name} = new $className;
            return $this->{$var_name};
        } catch(\Throwable $e) {
            throw new \Exception("[Controller] request access to an undefined property ({$var_name}) on controller class.");
        }
    }
}

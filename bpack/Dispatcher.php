<?php
namespace bPack;

use \Reflection, \ReflectionClass;
class Dispatcher implements Protocol\Dispatcher{
    protected Foundation $app;

    public function __construct(Foundation $app) {
        $this->app = $app;
    }

    public function dispatch(Protocol\RouteDestination $route):Protocol\Response {
        $currentChainTarget = null;

        // update namespace for
        $route->handler = array_filter(array_map(function($stop) use (&$currentChainTarget) {
            $isAcceptedType = ($stop instanceof \bPack\Protocol\Middleware) || is_string($stop);

            if(!$isAcceptedType) {
                throw new \Exception("[Dispatcher] Mux only accept string of target handler or Middleware");
            }

            // if already a object
            if(is_object($stop)) {
                return $stop;
            }

            // if two handler then error
            if(!is_null($currentChainTarget)) {
                throw new \Exception("[Dispatcher] Each Request Chain can only accept one target action.");
            }

            // if given string is not a fully qualified class name
            if(is_string($stop) && substr($stop, 0, 1) != "\\") {
                $stop = "\\App\\" . $stop;
            }

            $currentChainTarget = $stop;
        }, $route->handler), fn($r) => !is_null($r));

        // dessumble the handler (add before page after page then done)
        $route->handler[] = new TargetWrapperMiddleware(
            $this->app,
            $currentChainTarget,
            $route->params
        );

        // create request object used in this chain
        $request = new Request;

        // iterator over all middlewares
        $handler = new DispatchPipeline($route->handler, new PipelineNoResponseErrorMiddleware);
        return $handler->handle($request);
    }
}

class TargetWrapperMiddleware implements Protocol\Middleware {
    private string $targetClass;
    private string $targetAction;
    private array  $targetActionParams;
    private array  $targetActionCallParams;

    private Protocol\Controller $targetInstance;

    private Foundation $app;

    public function __construct(Foundation $app, string $wrapMethod, array $params) {
        $this->app = $app;
        $this->targetActionParams = $params;
        $this->methodExtractAndCheck($wrapMethod);
    }

    protected function methodExtractAndCheck(string $targetFQDN) {
        [$this->targetClass, $this->targetAction] = explode("#", $targetFQDN);
        $reflectClass = new ReflectionClass($this->targetClass);

        if(!$reflectClass->hasMethod($this->targetAction)) {
            throw new \Exception("[Dispatcher] Requested method [{$targetFQDN}] not found.");
        }

        $this->targetInstance = $reflectClass->newInstance($this->app);

         // check for the arguments, and fill the parameter in order.
         $methodArguments = $reflectClass->getMethod($this->targetAction)->getParameters();

         $callArguments = [];
         foreach ($methodArguments as $parameter) {
             array_push($callArguments, $this->targetActionParams[$parameter->name] ?? null);
         }

         $this->targetActionCallParams =  $callArguments;
    }

    public function process(Protocol\Request $req, Protocol\Pipeline $handler):Protocol\Response {
        $response = new Response;

        $this->targetInstance->__init($req, $response);
        $this->targetInstance->__exec($this->targetAction, $this->targetActionCallParams);

        return $response;
    }
}

class PipelineNoResponseErrorMiddleware implements Protocol\Middleware {
    public function process(Protocol\Request $req, Protocol\Pipeline $handler): Protocol\Response {
        $response = new Response;

        $response->status(500)->json([
            "status" => 500,
            "error" => "[Pipeline] no response were generate from request chain."
        ]);

        return $response;
    }
}

class DispatchPipeline implements Protocol\Pipeline {
    private array $items;

    public function __construct(array $items, Protocol\Middleware $fallback) {
        $this->items = $items;
        $this->fallback = $fallback;
    }

    // handle receive a list of items need to be processed within the pipeline
    // we should go over it
    public function handle(Protocol\Request $req): Protocol\Response {
        if( sizeof($this->items) === 0 ) {
            return $this->fallback->process($req, $this);
        }

        $item = array_shift($this->items);
        return $item->process($req, $this);
    }
}

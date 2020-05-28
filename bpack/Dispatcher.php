<?php
namespace bPack;
/*
    todo regrading dispather:
        build a state machine for each request chain
        middleware will contains like "Middleware\xxxxx" <-- middleware are required to implment Interface

        [
            middleware,
            middleware,
            Wrapper of [beforePage], <-- #
            Wrapper of [handler], <-- #
            Wrapper of [afterPage] <-- #
        ]

        each chain can only have one handler (PSR-14)

        at the end of this chain where there is no more state,
        dispatcher should send out the response to close the request.

        middleware can also abort the chain by calling `
            $this->chain->Abort();
*/

class Dispatcher implements Protocol\Dispatcher{
    public function dispatch(Protocol\RouteDestination $route):Protocol\Response {
        $currentChainHandler = null;

        // update namespace for
        $route->handler = array_filter(array_map(function($stop) use ($currentChainHandler) {
            $isAcceptedType = ($stop instanceof \bPack\Protocol\Middleware) || is_string($stop);

            if(!$isAcceptedType) {
                throw new \Exception("[Dispatcher] Mux only accept string of target handler or Middleware");
            }

            // if already a object
            if(is_object($stop)) {
                return $stop;
            }

            // if two handler then error
            if(!is_null($currentChainHandler)) {
                throw new \Exception("[Dispatcher] Each Request Chain can only accept one handler.");
            }

            // if given string is not a fully qualified class name
            if(is_string($stop) && substr($stop, 0, 1) != "\\") {
                $stop = "\\App\\" . $stop;
            }

            $currentChainHandler = $stop;
        }, $route->handler), fn($r) => !is_null($r));

        // dessumble the handler (add before page after page then done)
        var_dump($route);
        return new Response;
    }
}

class HandlerWrapper implements Protocol\Middleware {
    public function __construct(string $wrapMethod) {

    }
}

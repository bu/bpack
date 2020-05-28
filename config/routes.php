<?php

return function (bPack\Router\Mux &$r) {
    $r->use(new App\Middleware\Dummy);
    $r->get("/", new App\Middleware\Dummy, "Index#index");
};

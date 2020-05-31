<?php declare(strict_types=1);

return function (bPack\Router\Mux &$r) {
    $r->use(new App\Middleware\Dummy);
    $r->get("/", new App\Middleware\Dummy, "Index#index");
    $r->get("/hello/{name2}/{name}", new App\Middleware\Dummy, "Index#hello");
};

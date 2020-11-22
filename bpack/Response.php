<?php declare(strict_types=1);
namespace bPack;

class Response implements Protocol\Response {
    // int
    protected $status = 200;
    // ?Protocol\ResponseRenderer
    protected  $renderer = null;

    use Protocol\HookTrait;

    // for hooktrait
    public function getHooks():array {
        return [
            "beforeSend",
            "beforeRedirect"
        ];
    }

    public function redirect(string $uri): void {
        //run hook here
        $this->runHook("beforeRedirect");
        // actual redirect
        header('Location: ' . $uri, true, 302);
        exit;
    }

    public function send():void {
        http_response_code($this->status);
        header("Content-Type: " . $this->renderer->getContentType());

        //run hook here
        $this->runHook("beforeSend");

        // then send the response
        echo $this->renderer->render();
    }

    public function status(int $status_code):Protocol\Response {
        $this->status = $status_code;
        return $this;
    }

    public function json(array $data):Protocol\Response {
        $this->renderer = new Response\JSON($data);
        return $this;
    }

    public function text(string $data):Protocol\Response {
        $this->renderer = new Response\Text($data);
        return $this;
    }

    public function html(string $data):Protocol\Response {
        $this->renderer = new Response\HTML($data);
        return $this;
    }

    public function setRenderer(Protocol\ResponseRenderer $renderer):Protocol\Response {
        $this->renderer = $renderer;
        return $this;
    }

    public function __call(string $method, array $args):Protocol\Response {
        call_user_func_array([$this->renderer, $method], $args);
        return $this;
    }
}

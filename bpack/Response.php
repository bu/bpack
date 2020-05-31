<?php declare(strict_types=1);
namespace bPack;

class Response implements Protocol\Response {
    protected int $status = 200;
    protected ?Protocol\ResponseRenderer $renderer = null;

    public function send():void {
        http_response_code($this->status);
        header("Content-Type: " . $this->renderer->getContentType());
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

    public function __call(string $method, array $args):Protocol\Response {
        call_user_func_array([$this->renderer, $method], $args);
        return $this;
    }
}

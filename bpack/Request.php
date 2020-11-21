<?php declare(strict_types=1);
namespace bPack;

// TODO: PSR-7 should apply here
class Request implements Protocol\Request {
    public function get(string $key, $default_value = null, $options = array() ) {
        return $this->extractValue(INPUT_GET, $key, $default_value, $options);
    }

    public function post(string $key, $default_value = null, $options = array() ) {
        return $this->extractValue(INPUT_POST, $key, $default_value, $options);
    }

    public function cookie(string $key, $default_value = null, $options = array() ) {
        return $this->extractValue(INPUT_COOKIE, $key, $default_value, $options);
    }

    protected function extractValue(int $method, string $key, $default_value = null, $options = null) {

        $options = $option ?? [];
        $options["filter"] =  $options["filter"] ?? FILTER_SANITIZE_STRING;

        return filter_input($method, $key, $options["filter"]) ?? $default_value;
    }

}

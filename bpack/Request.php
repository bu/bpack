<?php declare(strict_types=1);
namespace bPack;

// TODO: PSR-7 should apply here
class Request implements Protocol\Request {

    public function get(string $key, $default_value = null, $options = array() ) {
        return $this->extractValue("GET", $default_value, $options);
    }

    public function post(string $key, $default_value = null, $options = array() ) {
        return $this->extractValue("POST", $default_value, $options);
    }

    public function cookie(string $key, $default_value = null, $options = array() ) {
        return $this->extractValue("COOKIE", $key, $default_value, $options);
    }

    protected function extractValue(string $method, string $key, $default_value = null, $options = null) {

        $options ??= [];
        $options["filter"] ??= FILTER_SANITIZE_STRING;
        $options["flag"] ??= [];

        // get the value
        if( !isset(${"_" . $method}[$key]) ) {
            return $default_value;
        }

        $value = ${"_" . $method}[$key];

        // if key is an array
        if( is_array($value) ) {
            $options["flag"][] = FILTER_REQUIRE_ARRAY;
        }

        return filter_var($value, $options["filter"], $options["flag"]);
    }

}

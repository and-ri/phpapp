<?php

class Env {
    protected $dotenv;

    public function __construct() {
        $this->dotenv = Dotenv\Dotenv::createImmutable(DIR_ROOT);
        $this->dotenv->safeLoad();
    }

    public function get($key, $default = null) {
        return isset($_ENV[$key]) && $_ENV[$key] !== '' ? $_ENV[$key] : $default;
    }
}

<?php

class Env {
    protected $dotenv;

    public function __construct() {
        $this->dotenv = Dotenv\Dotenv::createImmutable(DIR_ROOT);
        $this->dotenv->load();
    }

    public function get($key) {
        return !empty($_ENV[$key]) ? $_ENV[$key] : null;
    }
}
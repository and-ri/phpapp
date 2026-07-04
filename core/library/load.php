<?php

class Load {
    protected $registry;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function __get($key) {
        return $this->registry->get($key);
    }

    public function __set($name, $value) {
        $this->registry->set($name, $value);
    }

    public function controller($route, $data = array()) {
        $route = $this->validateRoute($route);

        $output = '';

        $file = DIR_CONTROLLER . $route . '.php';
        $class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $route);

        if (is_file($file)) {
            include_once($file);

            $controller = new $class($this->registry);

            if (is_callable(array($controller, 'index'))) {
                $output = call_user_func(array($controller, 'index'), $data);
            }
        }

        // Sub-controller output is trusted HTML: keep it unescaped
        // when passed into an autoescaped Twig template
        return new \Twig\Markup((string)$output, 'UTF-8');
    }

    public function model($route) {
        $route = $this->validateRoute($route);

        $file = DIR_MODEL . $route . '.php';
        $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $route);

        if (is_file($file)) {
            include_once($file);

            return new $class($this->registry);
        }

        throw new RuntimeException('Error: Could not load model ' . $route . '!');
    }

    protected function validateRoute($route) {
        $route = (string)$route;

        if (!preg_match('/^[a-zA-Z0-9_\/-]+$/', $route) || strpos($route, '..') !== false) {
            throw new InvalidArgumentException('Error: Invalid route ' . $route . '!');
        }

        return $route;
    }
}

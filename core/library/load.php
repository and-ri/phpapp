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
        $output = '';

        $file = DIR_CONTROLLER . str_replace(array('../', '..\\', '..'), '', $route) . '.php';
        $class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $route);

        if (is_file($file)) {
            include_once($file);

            $controller = new $class($this->registry);

            if (is_callable(array($controller, 'index'))) {
                $output = call_user_func(array($controller, 'index'), $data);
            }
        }

        return $output;
    }

    public function model($route) {
        $file = DIR_MODEL . str_replace(array('../', '..\\', '..'), '', $route) . '.php';
        $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $route);

        if (is_file($file)) {
            include_once($file);

            $model = new $class($this->registry);

            return $model;
        } else {
            trigger_error('Error: Could not load model ' . $route . '!');
            exit();
        }
    }
}
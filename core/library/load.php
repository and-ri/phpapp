<?php

class Load {
    protected $registry;
    
    public function __construct($registry) {
        $this->registry = $registry;
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
}
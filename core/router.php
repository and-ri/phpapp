<?php

class Router {
    private $registry;
    private $path;
    private $args = array();
    
    public $file;
    public $controller;
    public $action;
    
    function __construct($registry) {
        $this->registry = $registry;
    }
    
    public function start() {
        $this->getController();
        $this->executeController();
    }

    private function getController() {
        $route = (empty($_GET['rt'])) ? '' : $_GET['rt'];
        
        if (empty($route)) {
            $route = 'index';
        } else {
            $parts = explode('/', $route);
            $this->controller = $parts[0];

            if (isset($parts[1])) {
                $this->controller .= '/' . $parts[1];
            }

            if (isset($parts[2])) {
                $this->action = $parts[1];
            }
        }
        
        if (empty($this->controller)) {
            $this->controller = 'index';
        }
        
        if (empty($this->action)) {
            $this->action = 'index';
        }
        
        $this->file = DIR_CONTROLLER . $this->controller . '.php';
    }

    private function executeController() {
        if (is_readable($this->file) == false) {
            $this->file = DIR_CONTROLLER . 'error/controller.php';
            $this->controller = 'ErrorController';
        }
        
        require_once $this->file;

        $class_parts = explode('/', $this->controller);

        $class_string = '';

        foreach ($class_parts as $part) {
            $class_string .= ucfirst($part);
        }
        
        $class = 'Controller' . ucfirst($class_string);
        $controller = new $class($this->registry);
        
        if (is_callable(array($controller, $this->action)) == false) {
            $action = 'index';
        } else {
            $action = $this->action;
        }
        
        $controller->$action();
    }
}
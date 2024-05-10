<?php
/*
    FIXME:
        - We need to parse routes and execute the controller.
        /                   => index
        /index              => index.php => index
        /index              => index.php => index
        /catalog            => catalog.php => index
        /catalog/index      => catalog.php => index
        /catalog/components => (
            try catalog.php => components
            try catalog/components.php => index
        )
        /catalog/components/index => catalog/components.php => index
        /catalog/components/other => (
            try catalog/components.php => other
            try catalog/components/other.php => index
        )

        ...

*/
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
        $request = html_entity_decode($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');

        $route = empty($request) ? '' : $request;

        $route_parts = array_values(array_filter(explode('/', $route)));

        $this->file = implode('/', $route_parts);
        $this->controller = $route_parts;
    }

    private function executeController() {
        if (!file_exists(DIR_CONTROLLER . $this->file . '.php')) {
            $this->file = 'error/not_found';
            $this->controller = array('ErrorNotFound');
        }
    
        require_once DIR_CONTROLLER . $this->file . '.php';
    
        $class_string = '';
        $class_parts = explode('/', $this->file);
        
        foreach ($class_parts as $part) {
            $class_string .= ucfirst($part);
        }

        $class_string = str_replace(' ', '', ucwords(str_replace('_', ' ', $class_string)));

        $this->args = array(
            'controller' => $this->controller,
            'action' => $this->action
        );
        
        $class = 'Controller' . ucfirst($class_string);
        $controller = new $class($this->registry, $this->args);
        
        if (is_callable(array($controller, $this->action)) == false) {
            $this->action = 'index';
        }
        
        $action = $this->action;
        $controller->$action();
    }
    
}
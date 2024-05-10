<?php
/*
    FIXME:
        - We need to parse routes and execute the controller.
        EXAMPLES:
        /                   => index
        /index              => index.php => index
        /index              => index.php => index
        /catalog            => catalog.php => index
        /catalog/index      => catalog.php => index
        /catalog/components => (
            try catalog.php => components
            -> if not found 
               -> try catalog/components.php => index
                  -> if not found
                     -> error/not_found.php => index
        )
        /catalog/components/index => catalog/components.php => index
        /catalog/components/other => (
            try catalog/components.php => other
            -> if not found 
               -> try catalog/components/other.php => index
                    -> if not found
                         -> error/not_found.php => index
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
        // Get the request URI
        $request = html_entity_decode($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');

        // Split the URI into parts
        $route_parts = array_values(array_filter(explode('/', $request)));

        // Set default controller and action
        $this->file = 'index';
        $this->controller = 'ControllerIndex';
        $this->action = 'index';

        // Parse the route parts
        foreach ($route_parts as $key => $part) {
            $path = implode('/', array_slice($route_parts, 0, $key + 1));
            $controller_file = DIR_CONTROLLER . $path . '.php';

            if (file_exists($controller_file)) {
                $this->file = $path;
                $this->controller = 'Controller' . ucfirst(str_replace('/', '', $path));

                // Check if there's an action specified
                if (isset($route_parts[$key + 1])) {
                    $this->action = $route_parts[$key + 1];
                }
            }
        }

        // Set args for not_found
        if ($this->file == 'error/not_found') {
            $this->args['error_message'] = 'Controller or action not found';
        }
    }

    private function executeController() {
        $controller_file = DIR_CONTROLLER . $this->file . '.php';

        // Check if the controller file exists
        if (!file_exists($controller_file)) {
            $this->file = 'error/not_found';
            $this->controller = 'ErrorNotFoundController';
            $this->args['error_message'] = 'Controller file not found';
        }

        // Include the controller file
        require_once $controller_file;

        // Create the controller instance
        $controller = new $this->controller($this->registry, $this->args);

        // Check if the action method exists
        if (!method_exists($controller, $this->action)) {
            $this->action = 'index';
            $this->args['error_message'] = 'Action not found';
        }

        // Call the action method
        $action = $this->action;
        $controller->$action();
    }
}
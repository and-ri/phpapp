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
        // Get the request URI
        $request = html_entity_decode($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');

        // Split the URI into parts
        $route_parts = array_values(array_filter(explode('/', $request)));

        // Set default controller and action
        $this->file = 'index';
        $this->controller = 'ControllerIndex';
        $this->action = 'index';

        // If the request is empty (i.e., accessing the root), stop further processing
        if (empty($route_parts)) {
            return;
        }

        // Parse the route parts
        $current_path = '';
        $found_controller = false;

        foreach ($route_parts as $key => $part) {
            // Construct the current path
            $current_path = trim($current_path . '/' . $part, '/');
            $controller_file = DIR_CONTROLLER . $current_path . '.php';

            if (file_exists($controller_file)) {
                $this->file = $current_path;
                $this->controller = 'Controller' . implode('', array_map('ucfirst', explode('/', $current_path)));
                $found_controller = true;

                // Check if there's an action specified
                if (isset($route_parts[$key + 1])) {
                    $this->action = $route_parts[$key + 1];
                }

                break; // Stop further checks as we have found the correct controller
            }
        }

        // If no valid controller was found, redirect to error/not_found
        if (!$found_controller) {
            $this->args['controller'] = $current_path;
            $this->args['action'] = $this->action;
            $this->args['message'] = 'Controller not found';
            $this->file = 'error/not_found';
            $this->controller = 'ControllerErrorNotFound';
            $this->action = 'index';
        }
    }

    private function executeController() {
        $controller_file = DIR_CONTROLLER . $this->file . '.php';

        // Check if the controller file exists
        if (!file_exists($controller_file)) {
            $this->args['controller'] = $this->file;
            $this->args['action'] = $this->action;
            $this->args['message'] = 'Controller file not found';
            $this->file = 'error/not_found';
            $this->controller = 'ControllerErrorNotFound';
        } else {
            // Include the controller file
            require_once $controller_file;

            // Check if the controller class exists
            if (!class_exists($this->controller)) {
                require_once DIR_CONTROLLER . 'error/not_found.php';

                $this->args['controller'] = $this->file;
                $this->args['action'] = $this->action;
                $this->args['message'] = 'Controller class not found';
                $this->file = 'error/not_found';
                $this->controller = 'ControllerErrorNotFound';
            }
        }

        // Create the controller instance
        $controller = new $this->controller($this->registry, $this->args);

        // Check if the action method exists
        if (!method_exists($controller, $this->action)) {
            $this->args['controller'] = $this->file;
            $this->args['action'] = $this->action;
            $this->args['message'] = 'Action method not found';
            $this->file = 'error/not_found';
            require_once DIR_CONTROLLER . 'error/not_found.php';
            $this->controller = 'ControllerErrorNotFound';
            $controller = new $this->controller($this->registry, $this->args);
            $this->action = 'index';
        }

        // Call the action method
        $action = $this->action;
        $controller->$action();
    }
}

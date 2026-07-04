<?php

class Router {
    private $registry;
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
        // Get the request path (without query string) and decode it
        $request = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');

        // Split the URI into parts
        $route_parts = array_values(array_filter(explode('/', $request), 'strlen'));

        // Set default controller and action
        $this->file = 'index';
        $this->controller = 'ControllerIndex';
        $this->action = 'index';

        // If the request is empty (i.e., accessing the root), stop further processing
        if (empty($route_parts)) {
            return;
        }

        // Reject route parts with characters outside the allowed set
        // (protects against path traversal like /../config/web)
        foreach ($route_parts as $part) {
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $part)) {
                $this->setNotFound(implode('/', $route_parts), $this->action, 'Invalid route');
                return;
            }
        }

        // Parse the route parts
        $current_path = '';
        $found_controller = false;

        foreach ($route_parts as $key => $part) {
            // Construct the current path
            $current_path = trim($current_path . '/' . $part, '/');
            $controller_file = DIR_CONTROLLER . $current_path . '.php';

            if (is_file($controller_file)) {
                $this->file = $current_path;
                $this->controller = 'Controller' . implode('', array_map('ucfirst', explode('/', str_replace('_', '', $current_path))));

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
            $this->setNotFound($current_path, $this->action, 'Controller not found');
        }
    }

    private function setNotFound($controller, $action, $message) {
        $this->args['controller'] = $controller;
        $this->args['action'] = $action;
        $this->args['message'] = $message;
        $this->file = 'error/not_found';
        $this->controller = 'ControllerErrorNotFound';
        $this->action = 'index';
    }

    private function executeController() {
        // Fall back to the error controller if the file or class is missing
        if (!is_file(DIR_CONTROLLER . $this->file . '.php')) {
            $this->setNotFound($this->file, $this->action, 'Controller file not found');
        }

        require_once DIR_CONTROLLER . $this->file . '.php';

        if (!class_exists($this->controller)) {
            $this->setNotFound($this->file, $this->action, 'Controller class not found');
            require_once DIR_CONTROLLER . $this->file . '.php';
        }

        // Magic and internal methods (__construct, __get, ...) are not routable
        if (!$this->isRoutable($this->controller, $this->action)) {
            $this->setNotFound($this->file, $this->action, 'Action method not found');
            require_once DIR_CONTROLLER . $this->file . '.php';
        }

        // Create the controller instance and call the action method
        $controller = new $this->controller($this->registry, $this->args);

        $action = $this->action;
        $controller->$action();
    }

    private function isRoutable($class, $action) {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $action)) {
            return false;
        }

        if (!method_exists($class, $action)) {
            return false;
        }

        $method = new ReflectionMethod($class, $action);

        return $method->isPublic() && !$method->isStatic() && !$method->isConstructor() && !$method->isDestructor();
    }
}

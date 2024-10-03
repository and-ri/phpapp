<?php

class Controller {
    protected $registry;
    protected $args;

    protected $model;
    protected $data;

    public function __construct($registry, $args = array()) {
        $this->registry = $registry;
        $this->args = $args;

        $this->model = $this->app->getModels();
        $this->data = $this->app->getData();
    }

    public function __get($key) {
        return $this->registry->get($key);
    }
}
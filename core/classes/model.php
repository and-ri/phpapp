<?php

class Model {
    protected $registry;
    protected $model;
    
    public function __construct($registry) {
        $this->registry = $registry;

        $this->model = $this->app->getModels();
    }

    public function __get($key) {
        return $this->registry->get($key);
    }
}

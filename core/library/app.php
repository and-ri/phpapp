<?php

class App {
    private $registry;

    private $model;
    private $data;

    public function __construct($registry) {
        $this->registry = $registry;

        $this->model = new stdClass();
        $this->data = $this->language->all();
    }

    public function __get($key) {
        return $this->registry->get($key);
    }

    public function useModel($model) {
        $model_parts = explode('/', str_replace('../', '', (string)$model));
        
        switch (count($model_parts)) {
            case 1:
                $this->model->{$model_parts[0]} = $this->load->model($model_parts[0]);
                break;
            case 2:
                if (!isset($this->model->{$model_parts[0]})) {
                    $this->model->{$model_parts[0]} = new stdClass();
                }
                $this->model->{$model_parts[0]}->{$model_parts[1]} = $this->load->model($model_parts[0] . '/' . $model_parts[1]);
                break;
            case 3:
                if (!isset($this->model->{$model_parts[0]})) {
                    $this->model->{$model_parts[0]} = new stdClass();
                }
                if (!isset($this->model->{$model_parts[0]}->{$model_parts[1]})) {
                    $this->model->{$model_parts[0]}->{$model_parts[1]} = new stdClass();
                }
                $this->model->{$model_parts[0]}->{$model_parts[1]}->{$model_parts[2]} = $this->load->model($model_parts[0] . '/' . $model_parts[1] . '/' . $model_parts[2]);
                break;
            default:
                trigger_error('Error: Could not load model ' . $model . '!');
                exit();
        }
    }

    public function useLanguage($language) {
        $this->language->load($language);

        $this->data = array_merge($this->data, $this->language->all());
    }

    public function getModels() {
        return $this->model;
    }

    public function getData() {
        return $this->data;
    }
}
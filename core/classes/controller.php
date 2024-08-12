<?php

class Controller {
    protected $registry;
    protected $args;

    protected $request;
    protected $load;
    protected $language;
    protected $view;
    protected $page;
    protected $staticfile;
    protected $url;
    protected $session;
    protected $user;

    protected $model;

    protected $data = array();
    protected $use = array();

    public function __construct($registry, $args = array()) {
        $this->registry = $registry;
        $this->args = $args;

        $this->model = new stdClass();

        $this->request = $this->registry->get('request');
        $this->load = $this->registry->get('load');
        $this->language = $this->registry->get('language');
        $this->view = $this->registry->get('view');
        $this->page = $this->registry->get('page');
        $this->staticfile = $this->registry->get('staticfile');
        $this->url = $this->registry->get('url');
        $this->session = $this->registry->get('session');
        $this->user = $this->registry->get('user');

        $this->data = $this->language->all();
    }

    protected function useModel($model) {
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

    protected function useLanguage($language) {
        $this->language->load($language);

        $this->data = array_merge($this->data, $this->language->all());
    }
}
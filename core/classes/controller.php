<?php

class Controller {
    protected $registry;

    protected $input;
    protected $load;
    protected $language;
    protected $view;
    protected $page;

    protected $data = array();

    public function __construct($registry) {
        $this->registry = $registry;

        $this->input = $this->registry->get('input');
        $this->load = $this->registry->get('load');
        $this->language = $this->registry->get('language');
        $this->view = $this->registry->get('view');
        $this->page = $this->registry->get('page');

        $this->data = $this->language->all();
    }
}
<?php

class ControllerIndex extends Controller {
    public function index() {
        $this->response->setTitle('PHPapp :: MVC Framework');
        $this->response->setDescription('This is a simple page.');
        $this->response->setKeywords('hello, world');

        $this->data['heading_title'] = 'PHPapp';

        $this->data['header'] = $this->load->controller('common/header');

        $this->data['welcome'] = $this->load->controller('common/welcome');

        $this->data['footer'] = $this->load->controller('common/footer');

        $this->response->html($this->view->template('index', $this->data));
    }
}
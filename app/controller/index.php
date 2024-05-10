<?php

class ControllerIndex extends Controller {
    public function index() {
        $this->page->setTitle('PHPapp :: MVC Framework');
        $this->page->setDescription('This is a simple page.');
        $this->page->setKeywords('hello, world');

        $this->data['heading_title'] = 'PHPapp';

        $this->data['header'] = $this->load->controller('common/header');

        $this->data['welcome'] = $this->load->controller('common/welcome');

        $this->data['footer'] = $this->load->controller('common/footer');

        $this->page->render($this->view->template('index', $this->data));
    }
}
<?php

class ControllerErrorNotFound extends Controller {
    public function index() {
        $this->page->setTitle('PHPapp :: 404');
        $this->page->setDescription('This is a simple 404 page.');
        $this->page->setKeywords('hello, world');

        $this->data['heading_title'] = 'Error 404';

        $this->data['header'] = $this->load->controller('common/header');

        $this->data['welcome'] = $this->load->controller('common/welcome');

        $this->data['footer'] = $this->load->controller('common/footer');

        $this->data['controller'] = $this->args['controller'];
        $this->data['action'] = $this->args['action'];

        $this->page->addHeader('HTTP/1.1 404 Not Found');
        $this->page->render($this->view->template('error/not_found', $this->data));
    }
}
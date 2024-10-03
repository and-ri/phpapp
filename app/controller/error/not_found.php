<?php

class ControllerErrorNotFound extends Controller {
    public function index() {
        $this->page->setTitle('PHPapp :: 404');
        $this->page->setDescription('This is a simple 404 page.');
        $this->page->setKeywords('hello, world');

        $this->data['heading_title'] = 'Error 404';

        $this->page->addStyle('https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css');
        $this->page->addScript('https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js');

        $this->data['header'] = $this->load->controller('common/header');

        $this->data['footer'] = $this->load->controller('common/footer');

        $this->data['controller'] = isset($this->args['controller']) ? $this->args['controller'] : '';
        $this->data['action'] = isset($this->args['action']) ? $this->args['action'] : '';
        $this->data['message'] = isset($this->args['message']) ? $this->args['message'] : '';

        $this->page->addHeader('HTTP/1.1 404 Not Found');
        $this->page->render($this->view->template('error/not_found', $this->data));
    }
}

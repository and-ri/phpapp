<?php

class ControllerCatalogComponents extends Controller {
    public function index() {
        $this->page->setTitle('PHPapp :: Components');
        $this->page->setDescription('This is a simple components page.');
        $this->page->setKeywords('hello, world');

        $this->data['heading_title'] = 'Components';

        $this->data['header'] = $this->load->controller('common/header');

        $this->data['menu'] = $this->load->controller('common/menu');

        $this->data['footer'] = $this->load->controller('common/footer');

        $this->page->render($this->view->template('catalog/components', $this->data));
    }
}
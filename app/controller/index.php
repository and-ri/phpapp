<?php

class ControllerIndex extends Controller {
    public function index() {
        $this->response->setTitle($this->language->get('meta_title'));
        $this->response->setDescription($this->language->get('meta_description'));
        $this->response->setKeywords($this->language->get('meta_keywords'));

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['header'] = $this->load->controller('common/header');

        $this->data['welcome'] = $this->load->controller('common/welcome');

        $this->data['footer'] = $this->load->controller('common/footer');

        $this->response->html($this->view->template('index', $this->data));
    }
}
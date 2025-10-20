<?php

class ControllerIndex extends Controller {
    public function index() {
        $this->meta->setTitle($this->language->get('meta_title'));
        $this->meta->setDescription($this->language->get('meta_description'));

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['cover'] = $this->staticfile->getUri('img/welcome.png');

        $this->data['header'] = $this->load->controller('common/header');

        $this->data['welcome'] = $this->load->controller('common/welcome');

        $this->data['footer'] = $this->load->controller('common/footer');

        $this->response->html($this->view->template('index', $this->data));
    }
}
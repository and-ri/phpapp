<?php

class ControllerErrorNotFound extends Controller {
    public function index() {
        $this->app->useLanguage('error/not_found');

        $this->response->setTitle($this->language->get('meta_title'));
        $this->response->setDescription($this->language->get('meta_description'));
        $this->response->setKeywords($this->language->get('meta_keywords'));

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->response->addStyle('https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css');
        $this->response->addScript('https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js');

        $this->data['header'] = $this->load->controller('common/header');

        $this->data['footer'] = $this->load->controller('common/footer');

        $this->data['controller'] = isset($this->args['controller']) ? $this->args['controller'] : '';
        $this->data['action'] = isset($this->args['action']) ? $this->args['action'] : '';
        $this->data['message'] = isset($this->args['message']) ? $this->args['message'] : '';

        $this->response->addHeader('HTTP/1.1 404 Not Found');
        $this->response->html($this->view->template('error/not_found', $this->data));
    }
}

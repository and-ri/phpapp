<?php

class ControllerCatalogCsrfProtection extends Controller {
    protected $error = array();

    public function index() {
        $this->app->useLanguage('catalog/csrf_protection');

        $this->response->setTitle($this->language->get('meta_title'));
        $this->response->setDescription($this->language->get('meta_description'));
        $this->response->setKeywords($this->language->get('meta_keywords'));

        $this->data['action'] = $this->url->link('catalog/csrf_protection');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            $this->data['success'] = $this->language->get('text_success');
        } else {
            $this->data['error'] = isset($this->error['csrf_token']) ? $this->error['csrf_token'] : '';
            $this->data['email'] = isset($this->request->post['email']) ? $this->request->post['email'] : '';
        }

        $this->data['header'] = $this->load->controller('common/header');
        $this->data['footer'] = $this->load->controller('common/footer');

        $this->response->html($this->view->template('catalog/csrf_protection', $this->data));
    }

    protected function validate() {
        if (!isset($this->request->post['csrf']) || !$this->session->validateToken($this->request->post['csrf'])) {
            $this->error['csrf_token'] = $this->language->get('error_csrf_token');
        }

        return !$this->error;
    }
}
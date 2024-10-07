<?php

class ControllerCatalogPagination extends Controller {
    public function index() {
        $this->app->useLanguage('catalog/pagination');
        $this->app->useModel('demo/demo');
        
        $this->data = $this->language->all();

        $this->page->setTitle($this->language->get('meta_title'));
        $this->page->setDescription($this->language->get('meta_description'));
        $this->page->setKeywords($this->language->get('meta_keywords'));

        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

        $this->data['heading_title'] = $page > 1 ? $this->language->get('heading_title') . ' - ' . $page : $this->language->get('heading_title');


        $this->data['total'] = $this->model->demo->demo->getTotalPosts();

        $request_data = array(
            'start' => ($page - 1) * 10,
            'limit' => 10
        );

        $this->data['posts'] = $this->model->demo->demo->getPosts($request_data);

        $this->data['from'] = $request_data['start'];
        $this->data['to'] = $request_data['start'] + 10;

        $this->data['pagination'] = $this->load->controller('common/pagination', $this->pagination->get($this->url->link('catalog/pagination'), $this->data['total'], 10, $page));

        $this->data['header'] = $this->load->controller('common/header');
        $this->data['footer'] = $this->load->controller('common/footer');

        $this->page->render($this->view->template('catalog/pagination', $this->data));
    }
}
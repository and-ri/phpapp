<?php

class ControllerCommonMenu extends Controller {
    public function index() {
        $this->app->useLanguage('common/menu');

        $this->data['home'] = $this->url->link('');

        $this->data['links'] = array(
            array(
                'title' => $this->language->get('text_home'),
                'href' => $this->url->link('')
            ),
            array(
                'title' => $this->language->get('text_components'),
                'href' => $this->url->link('catalog/components')
            ),
            array(
                'title' => $this->language->get('text_pagination'),
                'href' => $this->url->link('catalog/pagination')
            ),
        );

        return $this->view->template('common/menu', $this->data);
    }
}
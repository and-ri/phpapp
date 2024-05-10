<?php

class ControllerCommonMenu extends Controller {
    public function index() {
        $this->data['home'] = $this->url->link('');

        $this->data['links'] = array(
            array(
                'title' => 'Home',
                'href' => $this->url->link('')
            ),
            array(
                'title' => 'Components',
                'href' => $this->url->link('catalog/components')
            ),
        );

        return $this->view->template('common/menu', $this->data);
    }
}
<?php

class ControllerCommonHeader extends Controller {
    public function index() {
        $this->data['meta'] = $this->meta->getMetaTags();

        $this->data['styles'] = $this->response->getStyles();

        $this->response->addScript('/assets/js/app.js');
        
        $scripts = $this->response->getScripts();

        $this->data['scripts'] = array();

        foreach ($scripts as $script) {
            if ($script['position'] == 'header') {
                $this->data['scripts'][] = $script['src'];
            }
        }

        $this->data['menu'] = $this->load->controller('common/menu');

        $this->response->addStyle('/assets/css/app.css');

        $this->data['styles'] = $this->response->getStyles();

        return $this->view->template('common/header', $this->data);
    }
}
<?php

class ControllerCommonHeader extends Controller {
    public function index() {
        $this->data['meta'] = $this->view->raw($this->meta->getMetaTags());

        $this->data['styles'] = $this->response->getStyles();

        $this->response->addScript($this->staticfile->getAssetUri('js/app.js'));
        
        $scripts = $this->response->getScripts();

        $this->data['scripts'] = array();

        foreach ($scripts as $script) {
            if ($script['position'] == 'header') {
                $this->data['scripts'][] = $script['src'];
            }
        }

        $this->data['menu'] = $this->load->controller('common/menu');

        $this->response->addStyle($this->staticfile->getAssetUri('css/app.css'));

        $this->data['styles'] = $this->response->getStyles();

        return $this->view->template('common/header', $this->data);
    }
}
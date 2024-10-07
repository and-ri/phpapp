<?php

class ControllerCommonHeader extends Controller {
    public function index() {
        $this->data['title'] = $this->response->getTitle();
        $this->data['description'] = $this->response->getDescription();
        $this->data['keywords'] = $this->response->getKeywords();
        $this->data['robots'] = $this->response->getRobots();
        $this->data['canonical'] = $this->response->getCanonical();
        $this->data['links'] = $this->response->getLinks();
        $this->data['styles'] = $this->response->getStyles();
        
        $scripts = $this->response->getScripts();

        $this->data['scripts'] = array();

        foreach ($scripts as $script) {
            if ($script['position'] == 'header') {
                $this->data['scripts'][] = $script['src'];
            }
        }

        $this->data['menu'] = $this->load->controller('common/menu');

        $this->response->addStyle('https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
        $this->response->addStyle('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css');
        $this->response->addStyle($this->staticfile->getUri('css/style.css'));

        $this->data['styles'] = $this->response->getStyles();

        return $this->view->template('common/header', $this->data);
    }
}
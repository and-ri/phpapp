<?php

class ControllerCommonHeader extends Controller {
    public function index() {
        $this->data['title'] = $this->page->getTitle();
        $this->data['description'] = $this->page->getDescription();
        $this->data['keywords'] = $this->page->getKeywords();
        $this->data['robots'] = $this->page->getRobots();
        $this->data['canonical'] = $this->page->getCanonical();
        $this->data['links'] = $this->page->getLinks();
        $this->data['styles'] = $this->page->getStyles();
        
        $scripts = $this->page->getScripts();

        $this->data['scripts'] = array();

        foreach ($scripts as $script) {
            if ($script['position'] == 'header') {
                $this->data['scripts'][] = $script['src'];
            }
        }

        $this->data['styles'][] = $this->staticfile->getUri('assets/bootstrap/css/bootstrap.min.css');
        $this->data['styles'][] = $this->staticfile->getUri('css/style.css');

        return $this->view->template('common/header', $this->data);
    }
}
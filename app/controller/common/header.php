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
        $this->data['scripts'] = $this->page->getScripts();

        return $this->view->template('common/header', $this->data);
    }
}
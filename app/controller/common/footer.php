<?php

class ControllerCommonFooter extends Controller {
    public function index() {
        $scripts = $this->page->getScripts();

        $this->data['scripts'] = array();

        foreach ($scripts as $script) {
            if ($script['position'] == 'footer') {
                $this->data['scripts'][] = $script['src'];
            }
        }

        $this->data['scripts'][] = $this->staticfile->getUri('assets/bootstrap/js/bootstrap.bundle.min.js');

        return $this->view->template('common/footer', $this->data);
    }
}
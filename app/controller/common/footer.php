<?php

class ControllerCommonFooter extends Controller {
    public function index() {
        $this->response->addScript('https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', 'footer');

        $scripts = $this->response->getScripts();

        $this->data['scripts'] = array();

        foreach ($scripts as $script) {
            if ($script['position'] == 'footer') {
                $this->data['scripts'][] = $script['src'];
            }
        }

        return $this->view->template('common/footer', $this->data);
    }
}
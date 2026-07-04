<?php

class ControllerCommonFooter extends Controller {
    public function index() {
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
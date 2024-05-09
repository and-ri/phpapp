<?php

class ControllerCommonFooter extends Controller {
    public function index() {
        $this->data['text_footer'] = 'Footer content';

        return $this->view->template('common/footer', $this->data);
    }
}
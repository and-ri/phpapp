<?php

class ControllerCommonWelcome extends Controller {
    public function index() {
        $this->app->useModel('user/user');

        $this->data['cover'] = $this->staticfile->getUri('img/welcome.png');

        return $this->view->template('common/welcome', $this->data);
    }
}
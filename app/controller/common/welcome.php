<?php

class ControllerCommonWelcome extends Controller {
    public function index() {
        $this->loadModel('user/user');
    }
}
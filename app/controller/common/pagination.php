<?php

class ControllerCommonPagination extends Controller {
    public function index($pagination) {
        $this->data['next'] = $pagination['next'];
        $this->data['last'] = $pagination['last'];
        $this->data['prev'] = $pagination['prev'];
        $this->data['first'] = $pagination['first'];
        $this->data['pages'] = $pagination['pages'];

        return $this->view->template('common/pagination', $this->data);
    }
}
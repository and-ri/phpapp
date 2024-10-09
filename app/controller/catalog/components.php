<?php

class ControllerCatalogComponents extends Controller {
    public function index() {
        $this->app->useLanguage('catalog/components');
        $this->data = $this->language->all();

        $this->response->setTitle($this->language->get('meta_title'));
        $this->response->setDescription($this->language->get('meta_description'));
        $this->response->setKeywords($this->language->get('meta_keywords'));

        $this->response->addStyle('https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css');
        $this->response->addScript('https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js');

        $libraries = scandir(DIR_CORE . 'library');

        $this->data['libraries'] = array();

        foreach ($libraries as $library) {
            if (is_file(DIR_CORE . 'library/' . $library)) {
                $class = ucfirst(substr($library, 0, -4));

                $methods = get_class_methods($class);

                $this->data['libraries'][$library] = 'class ' . $class . PHP_EOL . print_r($methods, true);
            }
        }

        $this->data['header'] = $this->load->controller('common/header');
        $this->data['footer'] = $this->load->controller('common/footer');

        $this->response->html($this->view->template('catalog/components', $this->data));
    }
}
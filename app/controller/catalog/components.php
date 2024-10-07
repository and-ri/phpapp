<?php

class ControllerCatalogComponents extends Controller {
    public function index() {
        $this->app->useLanguage('catalog/components');

        $this->page->setTitle($this->language->get('meta_title'));
        $this->page->setDescription($this->language->get('meta_description'));
        $this->page->setKeywords($this->language->get('meta_keywords'));

        $this->page->addStyle('https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css');
        $this->page->addScript('https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js');

        $this->data['heading_title'] = $this->language->get('heading_title');

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

        $this->page->render($this->view->template('catalog/components', $this->data));
    }
}
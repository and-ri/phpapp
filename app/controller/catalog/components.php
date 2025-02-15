<?php

class ControllerCatalogComponents extends Controller {
    public function index() {
        $this->app->useLanguage('catalog/components');

        $this->response->setTitle($this->language->get('meta_title'));
        $this->response->setDescription($this->language->get('meta_description'));
        $this->response->setKeywords($this->language->get('meta_keywords'));

        $this->response->addStyle('https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css');
        $this->response->addScript('https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js');

        $classes = scandir(DIR_CORE . 'classes');

        $this->data['classes'] = array();

        foreach ($classes as $class) {
            if (is_file(DIR_CORE . 'classes/' . $class)) {
                $methods = get_class_methods(substr($class, 0, -4));

                $this->data['classes'][$class] = json_encode(array(
                    'class' => substr($class, 0, -4),
                    'methods' => $methods
                ), JSON_PRETTY_PRINT);
            }
        }

        $libraries = scandir(DIR_CORE . 'library');

        $this->data['libraries'] = array();

        foreach ($libraries as $library) {
            if (is_file(DIR_CORE . 'library/' . $library)) {
                $class = ucfirst(substr($library, 0, -4));

                $methods = get_class_methods($class);

                $this->data['libraries'][$library] = json_encode(array(
                    'class' => $class,
                    'methods' => $methods
                ), JSON_PRETTY_PRINT);
            }
        }

        $this->data['header'] = $this->load->controller('common/header');
        $this->data['footer'] = $this->load->controller('common/footer');

        $this->response->html($this->view->template('catalog/components', $this->data));
    }
}
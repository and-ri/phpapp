<?php

class Language {
    private $lang;
    private $data = array();

    public function __construct($lang) {
        $this->lang = $lang;

        $this->load(DEFAULT_LANGUAGE);
    }

    public function get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : $key;
    }

    public function load($file) {
        $file = DIR_LANGUAGE . $this->lang . '/' . $file . '.php';

        if (file_exists($file)) {
            $data = include $file;

            $this->data = array_merge($this->data, $data);
        } else {
            trigger_error('Error: Could not load language ' . $file . '!');
            exit();
        }

        return $this;
    }

    public function all() {
        return $this->data;
    }
}
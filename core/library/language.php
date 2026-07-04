<?php

class Language {
    private $lang;
    private $data = array();

    public function __construct($lang) {
        $this->lang = $lang;

        // The base language file is named after the language code, e.g. en/en.php
        $this->load($this->lang);
    }

    public function get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : $key;
    }

    public function load($file) {
        if (!preg_match('/^[a-zA-Z0-9_\/-]+$/', (string)$file) || strpos($file, '..') !== false) {
            throw new InvalidArgumentException('Error: Invalid language route ' . $file . '!');
        }

        $path = DIR_LANGUAGE . $this->lang . '/' . $file . '.php';

        if (is_file($path)) {
            $data = include $path;

            if (is_array($data)) {
                $this->data = array_merge($this->data, $data);
            }
        } else {
            trigger_error('Error: Could not load language ' . $path . '!', E_USER_WARNING);
        }

        return $this;
    }

    public function all() {
        return $this->data;
    }
}

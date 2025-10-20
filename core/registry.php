<?php

class Registry {
    public $data = array();

    public function set($key, $value) {
        $this->data[$key] = $value;
    }

    public function get($key) {
        return $this->data[$key];
    }

    public function has($key) {
        return isset($this->data[$key]);
    }

    public function remove($key) {
        unset($this->data[$key]);
    }
}
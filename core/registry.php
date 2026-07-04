<?php

class Registry {
    private $data = array();

    public function set($key, $value) {
        $this->data[$key] = $value;
    }

    public function get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function has($key) {
        return isset($this->data[$key]);
    }

    public function remove($key) {
        unset($this->data[$key]);
    }
}

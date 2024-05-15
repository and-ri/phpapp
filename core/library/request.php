<?php

class Request {
    public $get;
    public $post;
    public $files;
    public $server;
    public $cookie;

    public function __construct() {
        $this->setGet();
        $this->setPost();
        $this->setFiles();
        $this->setServer();
        $this->setCookie();
    }

    private function setGet() {
        $this->get = $this->sanitize($_GET);
    }

    private function setPost() {
        $this->post = $this->sanitize($_POST);
    }

    private function setFiles() {
        $this->files = $_FILES;
    }

    private function setServer() {
        $this->server = $this->sanitize($_SERVER);
    }

    private function setCookie() {
        $this->cookie = $this->sanitize($_COOKIE);
    }
    
    private function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitize($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }
}
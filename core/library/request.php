<?php

class Request {
    public $get;
    public $post;
    public $files;
    public $server;
    public $cookie;

    public function __construct() {
        // Input is kept raw; escaping happens at the output layer
        // (Twig autoescape for HTML, Db::escape/execute for SQL)
        $this->get = $this->clean($_GET);
        $this->post = $this->clean($_POST);
        $this->files = $_FILES;
        $this->server = $this->clean($_SERVER);
        $this->cookie = $this->clean($_COOKIE);
    }

    private function clean($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->clean($value);
            }
        } else {
            // Strip null bytes and invalid UTF-8 sequences
            $data = str_replace("\0", '', (string)$data);

            if (!mb_check_encoding($data, 'UTF-8')) {
                $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            }
        }

        return $data;
    }
}

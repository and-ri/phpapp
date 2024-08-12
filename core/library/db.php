<?php

class Db {
    private $adaptor;

    public function __construct() {
        require_once DIR_CONFIG . 'database.php';
        
        $this->adaptor = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    }
    public function query($sql, $rows = false) {
        $result = $this->adaptor->query($sql);
        if ($result instanceof mysqli_result) {
            if ($rows) {
                $output = $result->num_rows ? $result->fetch_all(MYSQLI_ASSOC) : false;
            } else {
                $output = $result->num_rows ? $result->fetch_assoc() : false;
            }
        } else {
            $output = false;
        }
        return $output;
    }

    public function escape($value) {
        return $this->adaptor->real_escape_string($value);
    }

    public function last() {
        $last = $this->adaptor->insert_id;
        return $last;
    }
}
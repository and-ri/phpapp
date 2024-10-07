<?php

class Db {
    private $registry;
    private $adaptor;
    public $prefix;

    public function __construct($registry) {
        $this->registry = $registry;
        
        require_once DIR_CONFIG . 'database.php';

        $this->prefix = DB_PREFIX ? DB_PREFIX : $this->env->get('DB_PREFIX');

        $DB_HOST = DB_HOST ? DB_HOST : $this->env->get('DB_HOST');
        $DB_USER = DB_USER ? DB_USER : $this->env->get('DB_USER');
        $DB_PASS = DB_PASS ? DB_PASS : $this->env->get('DB_PASS');
        $DB_NAME = DB_NAME ? DB_NAME : $this->env->get('DB_NAME');
        $DB_PORT = DB_PORT ? DB_PORT : $this->env->get('DB_PORT');
        
        $this->adaptor = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
    }

    public function __get($key) {
        return $this->registry->get($key);
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

    public function count() {
        $count = $this->adaptor->affected_rows;
        return $count;
    }

    public function __destruct() {
        $this->adaptor->close();
    }
}
<?php

class Model {
    protected $registry;

    protected $db;
    
    public function __construct($registry) {
        $this->registry = $registry;

        $this->db = $this->registry->get('db');
    }
}

<?php

class User {
    protected $registry;

    public function __construct($registry) {
        $this->registry = $registry;
    }
}
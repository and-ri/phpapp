<?php

class User {
    protected $registry;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function register($username, $password) {
        $salt = substr(md5(uniqid(rand(), true)), 0, 9);

        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['salt' => $salt]);

        $this->registry->get('database')->insert('users', [
            'username' => $username,
            'password' => $password_hash,
            'salt' => $salt
        ]);
    }

    public function login($username, $password) {
        $user = $this->registry->get('database')->tables->users->where('username', '=', $username)->fetch();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $this->registry->get('session')->set('user_id', $user['_id']);
            }
        }
    }

    public function logout() {
        $this->registry->get('session')->destroy();
    }

    public function isLogged() {
        return $this->getId() ? true : false;
    }

    public function getId() {
        return $this->registry->get('session')->get('user_id');
    }
    
}
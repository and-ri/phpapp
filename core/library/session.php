<?php

class Session {
    protected $request;
    protected $registry;

    public function __construct($registry) {
        $this->registry = $registry;

        $this->request = $this->registry->get('request');

        $this->start();
    }

    public function start() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_only_cookies', 1);
        ini_set('session.use_trans_sid', 0);
        ini_set('session.save_handler', 'files');
        ini_set('session.save_path', DIR_SESSION);

        if (!is_dir(DIR_SESSION)) {
            mkdir(DIR_SESSION, 0700, true);
        }

        session_name(SESSION_NAME);

        session_start([
            'cookie_lifetime' => 86400,
            'cookie_path' => '/',
            'cookie_httponly' => true,
            'cookie_secure' => defined('SSL') && SSL,
            'cookie_samesite' => 'Strict',
            'use_strict_mode' => true,
            'sid_length' => 64,
            'sid_bits_per_character' => 6
        ]);

        if (!$this->has('token')) {
            $this->refreshToken();
        }
    }

    public function regenerate() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public function destroy() {
        if (session_id()) {
            session_destroy();

            setcookie(SESSION_NAME, '', [
                'expires' => time() - 42000,
                'path' => '/',
                'httponly' => true,
                'secure' => defined('SSL') && SSL,
                'samesite' => 'Strict'
            ]);
        }
    }

    public function getId() {
        return session_id();
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function remove($key) {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public function refreshToken() {
        $this->set('token', bin2hex(random_bytes(32)));
    }

    public function validateToken($token) {
        return is_string($token) && $this->has('token') && hash_equals((string)$this->get('token'), $token);
    }

    public function __destruct() {
        session_write_close();
    }
}

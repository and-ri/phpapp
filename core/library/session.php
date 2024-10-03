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
        if (isset($this->request->cookie[SESSION_NAME]) && $this->request->cookie[SESSION_NAME] && $this->request->cookie[SESSION_NAME] != session_id() && file_exists(DIR_SESSION . '/sess_' . $this->request->cookie[SESSION_NAME])) {
            session_id($this->request->cookie[SESSION_NAME]);
        }

        ini_set('session.use_only_cookies', 1);
        ini_set('session.use_trans_sid', 0);
        ini_set('session.save_handler', 'files');
        ini_set('session.save_path', DIR_SESSION);

        if (!is_dir(DIR_SESSION)) {
            mkdir(DIR_SESSION, 0777);
        }

        session_name(SESSION_NAME);

        session_set_cookie_params(0, '/');

        session_start([
            'cookie_lifetime' => 86400,
            'cookie_httponly' => true,
            'cookie_samesite' => 'Strict',
            'use_strict_mode' => true,
            'sid_length' => 64,
            'sid_bits_per_character' => 6
        ]);

        setcookie(SESSION_NAME, session_id(), 0, '/');
    }

    public function destroy() {
        if (session_id()) {
            session_destroy();
            
            setcookie(SESSION_NAME, '', time() - 42000, '/');
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

    public function __destruct() {
        session_write_close();
    }
}

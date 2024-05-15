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
        if (isset($this->request->cookie[SESSION_NAME])) {
            session_id($this->request->cookie[SESSION_NAME]);
        }

        if (!session_id()) {
            ini_set('session.use_only_cookies', 'On');

            session_name(SESSION_NAME);

            session_set_cookie_params(0, '/');

            session_start();
        }
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
}
<?php

/**
 * One-time session messages for the POST -> redirect -> GET pattern:
 *
 *   $this->flash->set('success', 'Saved!');
 *   $this->response->redirect($this->url->link('some/page'));
 *
 * and after the redirect:
 *
 *   $this->data['success'] = $this->flash->get('success');
 */
class Flash {
    protected $registry;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function set($key, $message) {
        $flash = $this->session()->get('_flash') ?: array();

        $flash[$key] = $message;

        $this->session()->set('_flash', $flash);
    }

    public function get($key, $default = '') {
        $flash = $this->session()->get('_flash') ?: array();

        if (!array_key_exists($key, $flash)) {
            return $default;
        }

        $message = $flash[$key];

        unset($flash[$key]);

        $this->session()->set('_flash', $flash);

        return $message;
    }

    public function has($key) {
        $flash = $this->session()->get('_flash') ?: array();

        return array_key_exists($key, $flash);
    }

    public function all() {
        $flash = $this->session()->get('_flash') ?: array();

        $this->session()->set('_flash', array());

        return $flash;
    }

    protected function session() {
        return $this->registry->get('session');
    }
}

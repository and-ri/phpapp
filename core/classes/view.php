<?php

class View {
    protected $registry;
    protected $twig;
    
    public function __construct($registry) {
        $this->registry = $registry;

        $loader = new \Twig\Loader\FilesystemLoader(DIR_VIEW);

        $this->twig = new \Twig\Environment($loader, [
            'cache' => DIR_CACHE . 'twig',
            'auto_reload' => true,
            'debug' => true,
            'autoescape' => false
        ]);
    }

    public function template($template, $data = []) {
        $template = $this->twig->load($template . '.twig');

        $this->filter($data);

        return $template->render($data);
    }

    protected function filter(&$data) {
        $data['csrf'] = '<input type="hidden" name="csrf" value="' . $this->registry->get('session')->get('token') . '" />';
    }
}
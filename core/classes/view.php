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
        ]);
    }

    public function template($template, $data = []) {
        $template = $this->twig->load($template . '.twig');
        return $template->render($data);
    }
}
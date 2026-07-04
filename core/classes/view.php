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
            'debug' => false,
            'autoescape' => 'html'
        ]);
    }

    public function template($template, $data = []) {
        $template = $this->twig->load($template . '.twig');

        $this->filter($data);

        return $template->render($data);
    }

    /**
     * Wrap a trusted HTML string so autoescaping does not escape it,
     * e.g. $this->data['header'] = $this->view->raw($html);
     */
    public function raw($html) {
        return new \Twig\Markup((string)$html, 'UTF-8');
    }

    protected function filter(&$data) {
        $token = htmlspecialchars((string)$this->registry->get('session')->get('token'), ENT_QUOTES, 'UTF-8');

        $data['csrf'] = $this->raw('<input type="hidden" name="csrf" value="' . $token . '" />');
    }
}

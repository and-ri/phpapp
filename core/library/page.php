<?php

class Page {
    protected $title;
    protected $description;
    protected $keywords;
    protected $robots;
    protected $canonical;
    protected $links = array();
    protected $styles = array();
    protected $scripts = array();
    protected $headers = array();

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setKeywords($keywords) {
        $this->keywords = $keywords;
    }

    public function setRobots($robots) {
        $this->robots = $robots;
    }

    public function setCanonical($canonical) {
        $this->canonical = $canonical;
    }

    public function addLink($href, $rel) {
        $this->links[] = array(
            'href' => $href,
            'rel' => $rel,
        );
    }

    public function addStyle($href) {
        $this->styles[] = $href;
    }

    public function addScript($src, $position = 'header') {
        $this->scripts[] = array(
            'src' => $src,
            'position' => $position,
        );
    }

    public function addHeader($header) {
        $this->headers[] = $header;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getKeywords() {
        return $this->keywords;
    }

    public function getRobots() {
        return $this->robots;
    }

    public function getCanonical() {
        return $this->canonical;
    }

    public function getLinks() {
        return $this->links;
    }

    public function getStyles() {
        return $this->styles;
    }

    public function getScripts() {
        return $this->scripts;
    }

    public function render($html) {
        foreach ($this->headers as $header) {
            header($header);
        }

        echo html_entity_decode($html, ENT_QUOTES, 'UTF-8');
    }

    public function responseJson($data, $pretty = false) {
        $this->addHeader('Content-Type: application/json');

        foreach ($this->headers as $header) {
            header($header);
        }

        echo json_encode($data, $pretty ? JSON_PRETTY_PRINT : 0);
    }
}

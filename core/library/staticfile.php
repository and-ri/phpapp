<?php

class StaticFile {
    protected $static_dir = DIR_STATIC;
    protected $static_www_dir = DIR_WWW . 'static/';

    public function __construct() {
        if (!is_dir($this->static_dir)) {
            mkdir($this->static_dir, 0755, true);
        }

        if (!is_dir($this->static_dir . 'css')) {
            mkdir($this->static_dir . 'css', 0755, true);
        }
    }

    public function getFile($file) {
        $file = $this->static_dir . $this->sanitize($file);

        if (is_file($file)) {
            return file_get_contents($file);
        }

        return false;
    }

    public function getUri($file) {
        $file = $this->sanitize($file);

        if (!is_file($this->static_dir . $file)) {
            return URL_STATIC . $file;
        }

        $this->saveFile($file);

        $last_modified = filemtime($this->static_dir . $file);

        return URL_STATIC . $file . '?last_modified=' . $last_modified;
    }

    /**
     * URI for a Vite-built asset in www/assets/ with a cache-busting
     * version parameter, e.g. getAssetUri('css/app.css')
     */
    public function getAssetUri($file) {
        $file = $this->sanitize($file);

        $path = DIR_WWW . 'assets/' . $file;

        $uri = '/assets/' . $file;

        if (is_file($path)) {
            $uri .= '?v=' . filemtime($path);
        }

        return $uri;
    }

    protected function sanitize($file) {
        return str_replace(array('..', "\0"), '', (string)$file);
    }

    private function saveFile($file) {
        if (!is_file($this->static_www_dir . $file)) {
            $dir = dirname($this->static_www_dir . $file);

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            copy($this->static_dir . $file, $this->static_www_dir . $file);
        } elseif (filemtime($this->static_dir . $file) > filemtime($this->static_www_dir . $file)) {
            copy($this->static_dir . $file, $this->static_www_dir . $file);
        }
    }
}

<?php

class StaticFile {
    protected $static_dir = DIR_STATIC;
    protected $static_www_dir = DIR_WWW . 'static/';
    
    public function __construct() {
        if (!file_exists($this->static_dir)) {
            mkdir($this->static_dir, 0755, true);
        }

        if (!file_exists($this->static_dir . 'css')) {
            mkdir($this->static_dir . 'css', 0755, true);
        }
    }

    public function getFile($file) {
        $file = $this->static_dir . $file;
        
        if (file_exists($file)) {
            return file_get_contents($file);
        }
        
        return false;
    }

    public function getUri($file) {
        $this->saveFile($file);

        $last_modified = filemtime($this->static_dir . $file);
        
        return URL_STATIC . $file . '?last_modified=' . $last_modified;
    }

    private function saveFile($file) {
        if (file_exists($this->static_dir . $file) && !file_exists($this->static_www_dir . $file)) {
            $dir = dirname($this->static_www_dir . $file);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            copy($this->static_dir . $file, $this->static_www_dir . $file);
        } elseif (file_exists($this->static_dir . $file) && file_exists($this->static_www_dir . $file)) {
            $last_modified = filemtime($this->static_dir . $file);
            $last_modified_www = filemtime($this->static_www_dir . $file);

            if ($last_modified > $last_modified_www) {
                copy($this->static_dir . $file, $this->static_www_dir . $file);
            }
        }
    }
}
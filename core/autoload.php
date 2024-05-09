<?php

function vendor_autoloader($class_name) {
    $file = DIR_CORE . 'vendor/autoload.php';

    if (file_exists($file)) {
        require_once $file;
    }
}

function classes_autoloader($class_name) {
    $file = DIR_CORE . 'classes/' . strtolower($class_name) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}

function library_autoloader($class_name) {
    $file = DIR_CORE . 'library/' . strtolower($class_name) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}

spl_autoload_register('vendor_autoloader');
spl_autoload_register('classes_autoloader');
spl_autoload_register('library_autoloader');
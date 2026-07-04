<?php

// Composer autoloader: load once instead of probing on every class miss
if (is_file(DIR_CORE . 'vendor/autoload.php')) {
    require_once DIR_CORE . 'vendor/autoload.php';
}

function classes_autoloader($class_name) {
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $class_name)) {
        return;
    }

    $file = DIR_CORE . 'classes/' . strtolower($class_name) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
}

function library_autoloader($class_name) {
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $class_name)) {
        return;
    }

    $file = DIR_CORE . 'library/' . strtolower($class_name) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
}

spl_autoload_register('classes_autoloader');
spl_autoload_register('library_autoloader');

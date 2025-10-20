<?php

// Check if .env file exists
if (!file_exists(__DIR__ . '/../.env')) {
    header('Location: /installer.php');
    exit;
}

// Include config files
require_once __DIR__ . '/../config/directories.php';
require_once DIR_CONFIG . 'web.php';

// Run application
require_once DIR_CORE . 'bootstrap.php';
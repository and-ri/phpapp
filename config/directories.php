<?php
// Define the directories
define('DIR_ROOT', realpath(__DIR__ . '/../'));
define('DIR_APP', DIR_ROOT . '/app/');
define('DIR_CACHE', DIR_ROOT . '/cache/');
define('DIR_CONFIG', DIR_ROOT . '/config/');
define('DIR_CORE', DIR_ROOT . '/core/');
define('DIR_DATABASE', DIR_ROOT . '/database/');
define('DIR_STATIC', DIR_ROOT . '/static/');
define('DIR_WWW', DIR_ROOT . '/www/');
define('DIR_DATA', DIR_ROOT . '/data/');

// Define the subdirectories
define('DIR_SESSION', DIR_DATA . 'session/');
define('DIR_CONTROLLER', DIR_APP . 'controller/');
define('DIR_MODEL', DIR_APP . 'model/');
define('DIR_VIEW', DIR_APP . 'view/');
define('DIR_LANGUAGE', DIR_APP . 'language/');
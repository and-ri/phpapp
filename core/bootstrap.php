<?php

require_once DIR_CORE . 'registry.php';
require_once DIR_CORE . 'autoload.php';
require_once DIR_CORE . 'router.php';

$registry = new Registry();

$log = new Log('phpapp', 'php.log');

set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($log) {
    $log->error("PHP Error: [$errno] $errstr in $errfile on line $errline");
    return false;
});

set_exception_handler(function ($exception) use ($log) {
    $log->exception($exception);
});

ini_set('log_errors', '1');
ini_set('error_log', DIR_LOG . 'php_errors.log');

$registry->set('log', new Log());
$registry->set('env', new Env());
$registry->set('request', new Request());
$registry->set('load', new Load($registry));
$registry->set('language', new Language(DEFAULT_LANGUAGE));
$registry->set('app', new App($registry));
$registry->set('response', new Response());
$registry->set('db', new Db($registry));
$registry->set('staticfile', new StaticFile());
$registry->set('url', new Url());
$registry->set('session', new Session($registry));
$registry->set('pagination', new Pagination());
$registry->set('google_auth', new google_auth($registry));
$registry->set('meta', new Meta());

$registry->set('view', new View($registry));

$route = new Router($registry);

$route->start();

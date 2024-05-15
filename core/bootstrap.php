<?php

require_once DIR_CORE . 'registry.php';
require_once DIR_CORE . 'autoload.php';
require_once DIR_CORE . 'router.php';

$registry = new Registry();

$registry->set('request', new Request());
$registry->set('session', new Session($registry));
$registry->set('load', new Load($registry));
$registry->set('language', new Language(DEFAULT_LANGUAGE));
$registry->set('page', new Page());
$registry->set('database', new Database());
$registry->set('staticfile', new StaticFile());
$registry->set('url', new Url());

$registry->set('view', new View($registry));

$route = new Router($registry);

$route->start();
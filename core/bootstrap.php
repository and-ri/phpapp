<?php

require_once DIR_CORE . 'registry.php';
require_once DIR_CORE . 'autoload.php';
require_once DIR_CORE . 'router.php';

$registry = new Registry();

$registry->set('input', new Input());
$registry->set('load', new Load($registry));
$registry->set('language', new Language(DEFAULT_LANGUAGE));
$registry->set('page', new Page());

$registry->set('view', new View($registry));

$route = new Router($registry);

$route->start();
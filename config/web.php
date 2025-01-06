<?php

define('SSL', false); // Set to true if you are using SSL
define('DOMAIN', ''); // Set the domain
define('DEFAULT_LANGUAGE', 'en'); // Set the default language
define('SESSION_NAME', 'PHPAPPSESSID'); // Set the session name

define('URL_WEBSITE', (SSL ? 'https://' : 'http://') . (DOMAIN ? DOMAIN : $_SERVER['HTTP_HOST']) . '/');
define('URL_STATIC', URL_WEBSITE . 'static/');
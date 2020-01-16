<?php declare(strict_types = 1);

define('ROOT_PATH', realpath(__DIR__));
define('ENV_PATH', ROOT_PATH . '/config/.env');
define('ROUTE_PATH', ROOT_PATH . '/config/routing.json');
define('DI_PATH', ROOT_PATH . '/config/di.json');
define('LOG_PATH', ROOT_PATH . '/var/log/');

require_once(__DIR__ . '/vendor/autoload.php');
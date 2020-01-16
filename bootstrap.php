<?php declare(strict_types = 1);

define('ROOT_PATH', realpath(__DIR__));
define('ENV_PATH', ROOT_PATH . '/.env');
define('ROUTE_PATH', ROOT_PATH . '/routing.json');
define('LOG_PATH', ROOT_PATH . '/var/log/');

require_once(__DIR__ . '/vendor/autoload.php');
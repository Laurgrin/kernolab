<?php
define("ROOT_PATH", realpath(__DIR__));
define("ENV_PATH", ROOT_PATH . "/.env");
define("ROUTE_PATH", ROOT_PATH . "/routing.json");

require_once(__DIR__ . "/vendor/autoload.php");
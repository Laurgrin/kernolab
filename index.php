<?php
/* Bootstrapping */
$rootPath = realpath(__DIR__);
define("ROOT_PATH", $rootPath);
define("ENV_PATH", ROOT_PATH . "/.env");

require_once(__DIR__ . "/Autoload/Autoload.php");

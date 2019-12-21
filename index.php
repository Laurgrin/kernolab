<?php
/* Bootstrapping */
$rootPath = realpath(__DIR__);
define("ROOT_PATH", $rootPath);
define("ENV_PATH", ROOT_PATH . "/.env");

require_once(__DIR__ . "/vendor/autoload.php");

$connection = new Kernolab\Model\DataSource\MySql\Connection();

var_dump(mysqli_get_host_info($connection->connect()));

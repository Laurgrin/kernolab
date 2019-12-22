<?php

/* Bootstrapping */
$rootPath = realpath(__DIR__);
define("ROOT_PATH", $rootPath);
define("ENV_PATH", ROOT_PATH . "/.env");

require_once(__DIR__ . "/vendor/autoload.php");

/* Routing handling should go here */
use Kernolab\Model\Entity\Transaction\Transaction;
$transaction = new Transaction(
    0,
    1,
    "test",
    20,
    "test",
    200,
    2,
    "test",
    "EUR"
);

$entityParser = new \Kernolab\Model\Entity\EntityParser();
var_dump($entityParser->getEntityProperties([$transaction]));
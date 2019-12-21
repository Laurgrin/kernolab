<?php

/* Bootstrapping */
$rootPath = realpath(__DIR__);
define("ROOT_PATH", $rootPath);
define("ENV_PATH", ROOT_PATH . "/.env");

require_once(__DIR__ . "/vendor/autoload.php");

$transaction = new \Kernolab\Model\Entity\Transaction\Transaction(
    1,
    1,
    "processing",
    2.05,
    "2019-12-12 09:00:00",
    "2019-12-13 09:00:00",
    "test",
    20.50,
    2,
    "Name Surname",
    "EUR"
);

$reflection = new ReflectionClass($transaction);
$properties = $reflection->getProperties();

foreach ($properties as $property) {
    $property->setAccessible(true);
    echo "{$property->getName()} => {$property->getValue($transaction)}" . PHP_EOL;
}

<?php

require_once(__DIR__ . "/Autoload/Autoload.php");

$request = $_SERVER['REQUEST_URI'];

echo "<pre>";
print_r($_SERVER);
echo "</pre>";
die();

if (file_exists(__DIR__ . "/.env")) {
    $envContents = file_get_contents(__DIR__ . "/.env");
} else {
    die(".env file is missing, cannot proceed.");
}

//TODO: Remove hardcoding of MySql DB
$credentials = json_decode($envContents, true)["db"];

$connection = new \Model\DataSource\MySqlConnection(
    $credentials["host"],
    $credentials["user"],
    $credentials["password"],
    $credentials["database"]
);

try {
    $connection = $connection->connect();
} catch (\Exception\MySqlConnectionException $e) {
    die($e->getMessage());
}

echo "<pre>";
print_r(mysqli_get_host_info($connection));
echo "</pre>";



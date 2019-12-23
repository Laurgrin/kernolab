<?php
require_once(__DIR__ . "/bootstrap.php");

$router = new \Kernolab\Routing\Router(new \Kernolab\Controller\JsonResponse());
$router->route($_SERVER["REQUEST_URI"], $_SERVER["REQUEST_METHOD"]);
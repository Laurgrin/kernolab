<?php declare(strict_types = 1);
require_once(__DIR__ . '/../../bootstrap.php');

$controller = new \Kernolab\Controller\Transaction\Process(new \Kernolab\Controller\JsonResponse());
$controller->execute(['limit' => 0]);
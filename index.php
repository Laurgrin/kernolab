<?php declare(strict_types = 1);

require_once(__DIR__ . '/bootstrap.php');

$container = new \Kernolab\Service\DependencyInjectionContainer();
try {
    /** @var \Kernolab\Routing\Router $router */
    $router = $container->get(\Kernolab\Routing\Router::class);
    $router->route($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (\Kernolab\Exception\ContainerException $e) {
    die($e->getMessage());
} catch (ReflectionException $e) {
    die($e->getMessage());
}
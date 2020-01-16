<?php declare(strict_types = 1);

require_once(__DIR__ . '/bootstrap.php');

/* Create a logger without using the container so in case container fails, we can log it */
$logger = new \Kernolab\Service\Logger();
try {
    $container = new \Kernolab\Service\Container();
    /** @var \Kernolab\Routing\Router $router */
    $router = $container->get(\Kernolab\Routing\Router::class);
    $router->route($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (\Kernolab\Exception\ContainerException $e) {
    $logger->log(\Kernolab\Service\Logger::SEVERITY_ERROR, $e->getMessage());
} catch (ReflectionException $e) {
    $logger->log(\Kernolab\Service\Logger::SEVERITY_ERROR, $e->getMessage());
} catch (JsonException $e) {
    $logger->log(\Kernolab\Service\Logger::SEVERITY_ERROR, $e->getMessage());
}
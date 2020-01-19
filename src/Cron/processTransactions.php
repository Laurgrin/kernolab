<?php declare(strict_types = 1);
require_once(__DIR__ . '/../../bootstrap.php');

$logger    = new \Kernolab\Service\Logger(); //In case we need to log a container error
$container = new \Kernolab\Service\Container();

try {
    /** @var \Kernolab\Service\TransactionService $transactionService */
    $transactionService = $container->get(\Kernolab\Service\TransactionService::class);
    $transactionService->processTransaction();
} catch (JsonException $e) {
    $logger->log(\Kernolab\Service\Logger::SEVERITY_ERROR, $e->getMessage());
} catch (\Kernolab\Exception\ContainerException $e) {
    $logger->log(\Kernolab\Service\Logger::SEVERITY_ERROR, $e->getMessage());
} catch (ReflectionException $e) {
    $logger->log(\Kernolab\Service\Logger::SEVERITY_ERROR, $e->getMessage());
} catch (\Kernolab\Exception\ConfigurationFileNotFoundException $e) {
    $logger->log(\Kernolab\Service\Logger::SEVERITY_ERROR, $e->getMessage());
} catch (\Kernolab\Exception\MySqlConnectionException $e) {
    $logger->log(\Kernolab\Service\Logger::SEVERITY_ERROR, $e->getMessage());
} catch (\Kernolab\Exception\MySqlPreparedStatementException $e) {
    $logger->log(\Kernolab\Service\Logger::SEVERITY_ERROR, $e->getMessage());
}
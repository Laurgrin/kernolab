<?php declare(strict_types = 1);

namespace Kernolab\Service;

use JsonException;
use Kernolab\Controller\JsonResponse;
use Kernolab\Exception\ConfigurationFileNotFoundException;
use Kernolab\Exception\ContainerException;
use Kernolab\Exception\DateTimeException;
use Kernolab\Exception\EntityNotFoundException;
use Kernolab\Exception\HourlyTransactionException;
use Kernolab\Exception\LifetimeTransactionAmountException;
use Kernolab\Exception\MySqlConnectionException;
use Kernolab\Exception\MySqlPreparedStatementException;
use Kernolab\Exception\RequestParameterException;
use Kernolab\Exception\TransactionConfirmationException;
use Kernolab\Exception\TransactionCreationException;
use Kernolab\Exception\UndefinedRouteException;
use ReflectionException;
use TypeError;

class ExceptionHandler
{
    /**
     * @var \Kernolab\Service\Logger
     */
    protected $logger;
    
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * Handles the RequestParameterException
     *
     * @param \Kernolab\Exception\RequestParameterException $exception
     * @param \Kernolab\Controller\JsonResponse             $jsonResponse
     */
    public function handleRequestParameterException(
        RequestParameterException $exception,
        JsonResponse $jsonResponse
    ): void {
        foreach ($exception->getMissingKeys() as $missingKey) {
            $jsonResponse->addError(400, sprintf('Missing required key %s', $missingKey));
        }
    }
    
    /**
     * Handles the DateTimeException
     *
     * @param \Kernolab\Exception\DateTimeException $exception
     * @param \Kernolab\Controller\JsonResponse     $jsonResponse
     */
    public function handleDateTimeException(DateTimeException $exception, JsonResponse $jsonResponse): void
    {
        $jsonResponse->addError(500, 'An internal error has occurred while processing the request.');
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
    }
    
    /**
     * Handles the HourlyTransactionException
     *
     * @param \Kernolab\Exception\HourlyTransactionException $exception
     * @param \Kernolab\Controller\JsonResponse              $jsonResponse
     */
    public function handleHourlyTransactionException(
        HourlyTransactionException $exception,
        JsonResponse $jsonResponse
    ): void {
        $jsonResponse->addError(403, $exception->getMessage());
    }
    
    /**
     * Handles the LifetimeTransactionAmountException
     *
     * @param \Kernolab\Exception\LifetimeTransactionAmountException $exception
     * @param \Kernolab\Controller\JsonResponse                      $jsonResponse
     */
    public function handleLifetimeTransactionAmountException(
        LifetimeTransactionAmountException $exception,
        JsonResponse $jsonResponse
    ): void {
        $jsonResponse->addError(403, $exception->getMessage());
    }
    
    /**
     * Handles the TransactionCreationException
     *
     * @param \Kernolab\Exception\TransactionCreationException $exception
     * @param \Kernolab\Controller\JsonResponse                $jsonResponse
     */
    public function handleTransactionCreationException(
        TransactionCreationException $exception,
        JsonResponse $jsonResponse
    ): void {
        $jsonResponse->addError(500, 'An internal error has occurred while processing the request.');
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
    }
    
    /**
     * Handles the MySqlPreparedStatementException
     *
     * @param \Kernolab\Exception\MySqlPreparedStatementException $exception
     * @param \Kernolab\Controller\JsonResponse                   $jsonResponse
     */
    public function handleMySqlPreparedStatementException(
        MySqlPreparedStatementException $exception,
        JsonResponse $jsonResponse
    ): void {
        $jsonResponse->addError(500, 'An internal error has occurred while processing the request.');
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
    }
    
    /**
     * Handles the TypeError
     *
     * @param \TypeError                        $error
     * @param \Kernolab\Controller\JsonResponse $jsonResponse
     */
    public function handleTypeError(TypeError $error, JsonResponse $jsonResponse): void
    {
        $jsonResponse->addError(500, 'An internal error has occurred while processing the request.');
        $this->logger->log(Logger::SEVERITY_ERROR, $error->getMessage());
    }
    
    /**
     * Handles the TransactionConfirmationException
     *
     * @param \Kernolab\Exception\TransactionConfirmationException $exception
     * @param \Kernolab\Controller\JsonResponse                    $jsonResponse
     */
    public function handleTransactionConfirmationException(
        TransactionConfirmationException $exception,
        JsonResponse $jsonResponse
    ): void {
        $jsonResponse->addError(403, $exception->getMessage());
    }
    
    /**
     * Handles the EntityNotFoundException
     *
     * @param \Kernolab\Exception\EntityNotFoundException $exception
     * @param \Kernolab\Controller\JsonResponse           $jsonResponse
     */
    public function handleEntityNotFoundException(EntityNotFoundException $exception, JsonResponse $jsonResponse): void
    {
        $jsonResponse->addError(404, $exception->getMessage());
    }
    
    /**
     * Handles the MySqlConnectionException
     *
     * @param \Kernolab\Exception\MySqlConnectionException $exception
     * @param \Kernolab\Controller\JsonResponse            $jsonResponse
     */
    public function handleMySqlConnectionException(
        MySqlConnectionException $exception,
        JsonResponse $jsonResponse
    ): void {
        $jsonResponse->addError(500, 'An internal error has occurred while processing the request.');
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
    }
    
    /**
     * Handles the ContainerException
     *
     * @param \Kernolab\Exception\ContainerException $exception
     * @param \Kernolab\Controller\JsonResponse      $jsonResponse
     */
    public function handleContainerException(ContainerException $exception, JsonResponse $jsonResponse): void
    {
        $jsonResponse->addError(500, 'An internal error has been encountered.');
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
    }
    
    /**
     * Handles the ReflectionException
     *
     * @param \ReflectionException              $exception
     * @param \Kernolab\Controller\JsonResponse $jsonResponse
     */
    public function handleReflectionException(ReflectionException $exception, JsonResponse $jsonResponse): void
    {
        $jsonResponse->addError(500, 'An internal error has been encountered.');
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
    }
    
    /**
     * Handles the JsonException. Yes, we are trying to return a json response if another fails.
     *
     * @param \JsonException                    $exception
     * @param \Kernolab\Controller\JsonResponse $jsonResponse
     */
    public function handleJsonException(JsonException $exception, JsonResponse $jsonResponse): void
    {
        $jsonResponse->addError(500, 'An internal error has been encountered.');
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
    }
    
    /**
     * Handle the ConfigurationFileNotFoundException
     *
     * @param \Kernolab\Exception\ConfigurationFileNotFoundException $exception
     * @param \Kernolab\Controller\JsonResponse                      $jsonResponse
     */
    public function handleConfigurationFileNotFoundException(
        ConfigurationFileNotFoundException $exception,
        JsonResponse $jsonResponse
    ): void {
        $jsonResponse->addError(500, 'An internal error has been encountered.');
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
    }
    
    /**
     * Handle the UndefinedRouteException
     *
     * @param \Kernolab\Exception\UndefinedRouteException $exception
     * @param \Kernolab\Controller\JsonResponse           $jsonResponse
     */
    public function handleUndefinedRouteException(UndefinedRouteException $exception, JsonResponse $jsonResponse): void
    {
        $jsonResponse->addError(404, $exception->getMessage());
    }
}
<?php declare(strict_types = 1);

namespace Kernolab\Service;

use JsonException;
use Kernolab\Controller\JsonResponse;
use Kernolab\Exception\ApiException;
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
    protected const INTERNAL_ERROR = 'An internal error has occurred while processing the request.';
    
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
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleRequestParameterException(RequestParameterException $exception): void {
        $missingKeys = '';
        foreach ($exception->getMissingKeys() as $missingKey) {
            $missingKeys .= sprintf('%s, ', $missingKey);
        }
        
        $message = sprintf('Missing required keys %s', $missingKeys);
        
        throw new ApiException($message, 400);
    }
    
    /**
     * Handles the DateTimeException
     *
     * @param \Kernolab\Exception\DateTimeException $exception
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleDateTimeException(DateTimeException $exception): void
    {
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
        throw new ApiException(self::INTERNAL_ERROR, 500);
    }
    
    /**
     * Handles the HourlyTransactionException
     *
     * @param \Kernolab\Exception\HourlyTransactionException $exception
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleHourlyTransactionException(HourlyTransactionException $exception): void {
        throw new ApiException($exception->getMessage(), 403);
    }
    
    /**
     * Handles the LifetimeTransactionAmountException
     *
     * @param \Kernolab\Exception\LifetimeTransactionAmountException $exception
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleLifetimeTransactionAmountException(LifetimeTransactionAmountException $exception): void {
        throw new ApiException($exception->getMessage(), 403);
    }
    
    /**
     * Handles the TransactionCreationException
     *
     * @param \Kernolab\Exception\TransactionCreationException $exception
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleTransactionCreationException(TransactionCreationException $exception): void {
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
        throw new ApiException(self::INTERNAL_ERROR, 500);
    }
    
    /**
     * Handles the MySqlPreparedStatementException
     *
     * @param \Kernolab\Exception\MySqlPreparedStatementException $exception
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleMySqlPreparedStatementException(MySqlPreparedStatementException $exception): void {
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
        throw new ApiException(self::INTERNAL_ERROR, 500);
    }
    
    /**
     * Handles the TypeError
     *
     * @param \TypeError $error
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleTypeError(TypeError $error): void
    {
        $this->logger->log(Logger::SEVERITY_ERROR, $error->getMessage());
        throw new ApiException(self::INTERNAL_ERROR, 500);
    }
    
    /**
     * Handles the TransactionConfirmationException
     *
     * @param \Kernolab\Exception\TransactionConfirmationException $exception
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleTransactionConfirmationException(TransactionConfirmationException $exception): void {
        throw new ApiException($exception->getMessage(), 403);
    }
    
    /**
     * Handles the EntityNotFoundException
     *
     * @param \Kernolab\Exception\EntityNotFoundException $exception
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleEntityNotFoundException(EntityNotFoundException $exception): void
    {
        throw new ApiException($exception->getMessage(), 404);
    }
    
    /**
     * Handles the MySqlConnectionException
     *
     * @param \Kernolab\Exception\MySqlConnectionException $exception
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleMySqlConnectionException(MySqlConnectionException $exception): void {
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
        throw new ApiException(self::INTERNAL_ERROR, 500);
    }
    
    /**
     * Handles the ContainerException
     *
     * @param \Kernolab\Exception\ContainerException $exception
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleContainerException(ContainerException $exception): void
    {
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
        throw new ApiException(self::INTERNAL_ERROR, 500);
    }
    
    /**
     * Handles the ReflectionException
     *
     * @param \ReflectionException $exception
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleReflectionException(ReflectionException $exception): void
    {
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
        throw new ApiException(self::INTERNAL_ERROR, 500);
    }
    
    /**
     * Handles the JsonException. Yes, we are trying to return a json response if another fails.
     *
     * @param \JsonException $exception
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleJsonException(JsonException $exception): void
    {
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
        throw new ApiException(self::INTERNAL_ERROR, 500);
    }
    
    /**
     * Handle the ConfigurationFileNotFoundException
     *
     * @param \Kernolab\Exception\ConfigurationFileNotFoundException $exception
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleConfigurationFileNotFoundException(ConfigurationFileNotFoundException $exception): void {
        $this->logger->log(Logger::SEVERITY_ERROR, $exception->getMessage());
        throw new ApiException(self::INTERNAL_ERROR, 500);
    }
    
    /**
     * Handle the UndefinedRouteException
     *
     * @param \Kernolab\Exception\UndefinedRouteException $exception
     *
     * @throws \Kernolab\Exception\ApiException
     */
    public function handleUndefinedRouteException(UndefinedRouteException $exception): void
    {
        throw new ApiException($exception->getMessage(), 404);
    }
}
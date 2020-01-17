<?php declare(strict_types = 1);

namespace Kernolab\Service;

use Kernolab\Controller\JsonResponse;
use Kernolab\Exception\DateTimeException;
use Kernolab\Exception\HourlyTransactionException;
use Kernolab\Exception\LifetimeTransactionAmountException;
use Kernolab\Exception\MySqlPreparedStatementException;
use Kernolab\Exception\RequestParameterException;
use Kernolab\Exception\TransactionConfirmationException;
use Kernolab\Exception\TransactionCreationException;

class TransactionControllerExceptionHandler
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
     * @param \TypeError $error
     * @param \Kernolab\Controller\JsonResponse $jsonResponse
     */
    public function handleTypeError(\TypeError $error, JsonResponse $jsonResponse): void
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
}
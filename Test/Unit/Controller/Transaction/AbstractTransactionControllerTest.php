<?php declare(strict_types = 1);

namespace Test\Unit\Controller\Transaction;

use Kernolab\Exception\ApiException;
use Kernolab\Exception\RequestParameterException;
use Kernolab\Model\Entity\Transaction\Transaction;
use Kernolab\Service\ExceptionHandler;
use Kernolab\Service\RequestValidator;
use Kernolab\Service\TransactionService;
use PHPUnit\Framework\TestCase;

abstract class AbstractTransactionControllerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|RequestValidator
     */
    protected $requestValidator;
    
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Transaction
     */
    protected $transaction;
    
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|TransactionService
     */
    protected $transactionService;
    
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ExceptionHandler
     */
    protected $exceptionHandler;
    
    protected $controller;
    
    protected function setUp(): void
    {
        $requestParameterException = $this->createStub(RequestParameterException::class);
        $this->requestValidator    = $this->createMock(RequestValidator::class);
        $this->requestValidator->method('validateRequest')
                               ->willReturnCallback(static function($params, $requiredKeys) use (
                                   $requestParameterException
                               ) {
                                   if (count($params) < count($requiredKeys)) {
                                       throw $requestParameterException;
                                   }
                               }
                               );
        
        $this->transaction = $this->createMock(Transaction::class);
        $this->transaction->method('getEntityId')->willReturn(1);
        
        $this->transactionService = $this->createMock(TransactionService::class);
        
        $apiException = $this->createStub(ApiException::class);
        $this->exceptionHandler = $this->createMock(ExceptionHandler::class);
        $this->exceptionHandler->method('handleTransactionConfirmationException')
                               ->willThrowException($apiException);
        $this->exceptionHandler->method('handleRequestParameterException')
                               ->willThrowException($apiException);
        $this->exceptionHandler->method('handleEntityNotFoundException')
                               ->willThrowException($apiException);
        $this->exceptionHandler->method('handleHourlyTransactionException')
                               ->willThrowException($apiException);
        $this->exceptionHandler->method('handleLifetimeTransactionAmountException')
                               ->willThrowException($apiException);
    }
    
    protected function tearDown(): void
    {
       $this->controller = null;
    }
}
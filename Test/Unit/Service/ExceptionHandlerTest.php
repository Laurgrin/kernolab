<?php declare(strict_types = 1);

namespace Unit\Service;

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
use Kernolab\Service\ExceptionHandler;
use Kernolab\Service\Logger;
use PHPUnit\Framework\TestCase;

class ExceptionHandlerTest extends TestCase
{
    private $exceptionHandler;
    
    protected function setUp(): void
    {
        $this->exceptionHandler = new ExceptionHandler(new Logger());
    }
    
    protected function tearDown(): void
    {
        $this->exceptionHandler = null;
    }
    
    public function testHandleEntityNotFoundException(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(EntityNotFoundException::class);
        $this->exceptionHandler->handleEntityNotFoundException($exception);
    }
    
    public function testHandleHourlyTransactionException(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(HourlyTransactionException::class);
        $this->exceptionHandler->handleHourlyTransactionException($exception);
    }
    
    public function testHandleRequestParameterException(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(RequestParameterException::class);
        $this->exceptionHandler->handleRequestParameterException($exception);
    }
    
    public function testHandleTypeError(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(\TypeError::class);
        $this->exceptionHandler->handleTypeError($exception);
    }
    
    public function testHandleReflectionException(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(\ReflectionException::class);
        $this->exceptionHandler->handleReflectionException($exception);
    }
    
    public function testHandleJsonException(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(\JsonException::class);
        $this->exceptionHandler->handleJsonException($exception);
    }
    
    public function testHandleConfigurationFileNotFoundException(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(ConfigurationFileNotFoundException::class);
        $this->exceptionHandler->handleConfigurationFileNotFoundException($exception);
    }
    
    public function testHandleDateTimeException(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(DateTimeException::class);
        $this->exceptionHandler->handleDateTimeException($exception);
    }
    
    public function testHandleLifetimeTransactionAmountException(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(LifetimeTransactionAmountException::class);
        $this->exceptionHandler->handleLifetimeTransactionAmountException($exception);
    }
    
    public function testHandleMySqlConnectionException(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(MySqlConnectionException::class);
        $this->exceptionHandler->handleMySqlConnectionException($exception);
    }
    
    public function testHandleMySqlPreparedStatementException(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(MySqlPreparedStatementException::class);
        $this->exceptionHandler->handleMySqlPreparedStatementException($exception);
    }
    
    public function testHandleContainerException(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(ContainerException::class);
        $this->exceptionHandler->handleContainerException($exception);
    }
    
    public function testHandleUndefinedRouteException(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(UndefinedRouteException::class);
        $this->exceptionHandler->handleUndefinedRouteException($exception);
    }
    
    public function testHandleTransactionCreationException(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(TransactionCreationException::class);
        $this->exceptionHandler->handleTransactionCreationException($exception);
    }
    
    public function testHandleTransactionConfirmationException(): void
    {
        $this->expectException(ApiException::class);
        $exception = $this->createStub(TransactionConfirmationException::class);
        $this->exceptionHandler->handleTransactionConfirmationException($exception);
    }
}

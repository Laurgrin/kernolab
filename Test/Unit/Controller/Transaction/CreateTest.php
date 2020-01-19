<?php declare(strict_types = 1);

namespace Test\Unit\Controller\Transaction;

use Kernolab\Controller\JsonResponse;
use Kernolab\Controller\Transaction\Create;
use Kernolab\Exception\ApiException;
use Kernolab\Exception\HourlyTransactionException;
use Kernolab\Exception\LifetimeTransactionAmountException;

require_once('AbstractTransactionControllerTest.php');

class CreateTest extends AbstractTransactionControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $hourlyTransactionException = $this->createStub(HourlyTransactionException::class);
        $transactionService = $this->transactionService;
        $hourlyTransactionClosure   = static function($userId) use ($hourlyTransactionException, $transactionService) {
            if ((int)$userId === 2) {
                throw $hourlyTransactionException;
            }
            
            return $transactionService;
        };
        
        $lifetimeTransactionException = $this->createStub(LifetimeTransactionAmountException::class);
        $lifetimeTransactionClosure   = static function($userId) use ($lifetimeTransactionException, $transactionService) {
            if ((int)$userId === 3) {
                throw $lifetimeTransactionException;
            }
    
            return $transactionService;
        };
        
        $this->transactionService->method('checkUserTransactionCount')
                                 ->willReturnCallback($hourlyTransactionClosure);
        $this->transactionService->method('checkUserLifetimeTransactionAmount')
                                 ->willReturnCallback($lifetimeTransactionClosure);
        $this->transactionService->method('setTransactionFee')
                                 ->willReturn($transactionService);
        $this->transactionService->method('createTransaction')
                                 ->willReturn($this->transaction);
        
        $this->controller = new Create(
            new JsonResponse(),
            $this->requestValidator,
            $transactionService,
            $this->exceptionHandler
        );
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecuteMissingArgs(): void
    {
        $input = [
            'transaction_details'        => '',
            'transaction_recipient_id'   => '',
            'transaction_recipient_name' => '',
            'transaction_amount'         => '',
            'transaction_currency'       => '',
        ];
        
        $this->expectException(ApiException::class);
        $this->controller->execute($input);
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecuteTransactionLimit(): void
    {
        $input = [
            'user_id'                    => 2,
            'transaction_details'        => '',
            'transaction_recipient_id'   => '',
            'transaction_recipient_name' => '',
            'transaction_amount'         => '',
            'transaction_currency'       => '',
        ];
        
        $this->expectException(ApiException::class);
        $this->controller->execute($input);
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecuteLifetimeLimit(): void
    {
        $input = [
            'user_id'                    => 3,
            'transaction_details'        => '',
            'transaction_recipient_id'   => '',
            'transaction_recipient_name' => '',
            'transaction_amount'         => '',
            'transaction_currency'       => '',
        ];
        
        $this->expectException(ApiException::class);
        $this->controller->execute($input);
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecute(): void
    {
        $input = [
            'user_id'                    => 1,
            'transaction_details'        => '',
            'transaction_recipient_id'   => '',
            'transaction_recipient_name' => '',
            'transaction_amount'         => 100,
            'transaction_currency'       => '',
        ];
        
        $expected = new JsonResponse();
        $expected->addField('status', 'success')
                 ->addField('code', 200)
                 ->addField('message', 'Transaction created successfully.')
                 ->addField('entity_id', 1);
        
        $this->assertEquals($expected, $this->controller->execute($input));
    }
}

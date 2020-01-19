<?php declare(strict_types = 1);

namespace Test\Unit\Controller\Transaction;

use Kernolab\Controller\JsonResponse;
use Kernolab\Controller\Transaction\Get;
use Kernolab\Exception\ApiException;
use Kernolab\Exception\EntityNotFoundException;
use Kernolab\Model\Entity\Transaction\TransactionRepository;
use PHPUnit\Util\Json;

require_once('AbstractTransactionControllerTest.php');

class GetTest extends AbstractTransactionControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->transaction->method('getEntityId')->willReturn(1);
        $this->transaction->method('getUserId')->willReturn(1);
        $this->transaction->method('getTransactionStatus')->willReturn('status');
        $this->transaction->method('getTransactionFee')->willReturn(1.00);
        $this->transaction->method('getCreatedAt')->willReturn('123');
        $this->transaction->method('getUpdatedAt')->willReturn('456');
        $this->transaction->method('getTransactionProvider')->willReturn('provider');
        $this->transaction->method('getTransactionAmount')->willReturn(10.00);
        $this->transaction->method('getTransactionRecipientId')->willReturn(2);
        $this->transaction->method('getTransactionRecipientName')->willReturn('name');
        $this->transaction->method('getTransactionCurrency')->willReturn('currency');
        $this->transaction->method('getTransactionDetails')->willReturn('details');
        
        $transaction = $this->transaction;
        $exception   = $this->createStub(EntityNotFoundException::class);
        
        $this->transactionService->method('getTransactionByEntityId')
                                 ->willReturnCallback(static function($entityId) use ($transaction, $exception) {
                                     if ((int)$entityId === 1) {
                                         return $transaction;
                                     }
            
                                     throw $exception;
                                 }
                                 );
        
        $this->controller = new Get(
            new JsonResponse(),
            $this->requestValidator,
            $this->transactionService,
            $this->exceptionHandler
        );
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecuteMissingParams(): void
    {
        $input = [];
        
        $this->expectException(ApiException::class);
        $this->controller->execute($input);
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecuteNotFound(): void
    {
        $input = ['entity_id' => 2];
    
        $this->expectException(ApiException::class);
        $this->controller->execute($input);
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecute()
    {
        $input = ['entity_id' => 1];
        $expected = new JsonResponse();
        $expected->addField('entity_id', $this->transaction->getEntityId())
                 ->addField('user_id', $this->transaction->getUserId())
                 ->addField('transaction_status', $this->transaction->getTransactionStatus())
                 ->addField('transaction_fee', $this->transaction->getTransactionFee())
                 ->addField('created_at', $this->transaction->getCreatedAt())
                 ->addField('updated_at', $this->transaction->getUpdatedAt())
                 ->addField('transaction_provider', $this->transaction->getTransactionProvider())
                 ->addField('transaction_amount', $this->transaction->getTransactionAmount())
                 ->addField('transaction_recipient_id', $this->transaction->getTransactionRecipientId())
                 ->addField('transaction_recipient_name', $this->transaction->getTransactionRecipientName())
                 ->addField('transaction_currency', $this->transaction->getTransactionCurrency())
                 ->addField('transaction_details', $this->transaction->getTransactionDetails());
        
        $this->assertEquals($expected, $this->controller->execute($input));
    }
}

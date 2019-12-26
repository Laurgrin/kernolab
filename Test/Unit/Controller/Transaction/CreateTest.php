<?php

namespace Test\Unit\Controller\Transaction;

use Kernolab\Controller\JsonResponse;
use Kernolab\Controller\Transaction\Create;
use Kernolab\Model\Entity\Transaction\Transaction;
use Kernolab\Model\Entity\Transaction\TransactionRepository;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    private $mockController;
    
    protected function setUp(): void
    {
        $transaction = $this->createMock(Transaction::class);
        $transaction->method("getEntityId")
                    ->willReturn(1);
        
        $repository = $this->createMock(TransactionRepository::class);
        $repository->method("createTransaction")
                   ->willReturn($transaction);
        
        $this->mockController = $this->getMockBuilder(Create::class)
                                     ->setMethods(["getTransactionCount", "canTransfer", "getTransactionFee"])
                                     ->setConstructorArgs([new JsonResponse(), $repository])
                                     ->getMock();
    }
    
    protected function tearDown(): void
    {
        $this->mockController = null;
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecuteMissingArgs()
    {
        $input = [
            "transaction_details"        => "",
            "transaction_recipient_id"   => "",
            "transaction_recipient_name" => "",
            "transaction_amount"         => "",
            "transaction_currency"       => "",
        ];
        
        $expected = '{"status":"error","errors":[{"code":400,"message":"Missing required argument user_id."}]}';
        
        $this->expectOutputString($expected);
        $this->mockController->execute($input);
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecuteTransactionLimit()
    {
        $input = [
            "user_id"                    => 1,
            "transaction_details"        => "",
            "transaction_recipient_id"   => "",
            "transaction_recipient_name" => "",
            "transaction_amount"         => "",
            "transaction_currency"       => "",
        ];
        
        $expected = '{"status":"error","errors":[{"code":403,"message":"Hourly transaction limit exceeded."}]}';
        $this->mockController->expects($this->once())
                             ->method("getTransactionCount")
                             ->with(1)
                             ->willReturn(11);
        
        $this->expectOutputString($expected);
        $this->mockController->execute($input);
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecuteLifetimeLimit()
    {
        $input = [
            "user_id"                    => 1,
            "transaction_details"        => "",
            "transaction_recipient_id"   => "",
            "transaction_recipient_name" => "",
            "transaction_amount"         => "",
            "transaction_currency"       => "",
        ];
        
        $expected = '{"status":"error","errors":[{"code":403,"message":"Maximum lifetime transactions reached."}]}';
        $this->mockController->expects($this->once())
                             ->method("getTransactionCount")
                             ->with(1)
                             ->willReturn(7);
        
        $this->mockController->expects($this->once())
                             ->method("canTransfer")
                             ->with(1)
                             ->willReturn(false);
        
        $this->expectOutputString($expected);
        $this->mockController->execute($input);
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecute()
    {
        $input = [
            "user_id"                    => 1,
            "transaction_details"        => "",
            "transaction_recipient_id"   => "",
            "transaction_recipient_name" => "",
            "transaction_amount"         => 100,
            "transaction_currency"       => "",
        ];
        
        $expected = '{"status":"success","code":200,"message":"Transaction created successfully.","entity_id":1}';
        $this->mockController->expects($this->once())
                             ->method("getTransactionCount")
                             ->with(1)
                             ->willReturn(7);
        
        $this->mockController->expects($this->once())
                             ->method("canTransfer")
                             ->with(1)
                             ->willReturn(true);
        
        $this->mockController->expects($this->once())
                             ->method("getTransactionFee")
                             ->with(1, 100)
                             ->willReturn(10.00);
        
        $this->expectOutputString($expected);
        $this->mockController->execute($input);
    }
}

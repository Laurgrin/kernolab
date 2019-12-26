<?php

namespace Test\Unit\Controller\Transaction;

use Kernolab\Controller\JsonResponse;
use Kernolab\Controller\Transaction\Get;
use Kernolab\Model\Entity\Transaction\Transaction;
use Kernolab\Model\Entity\Transaction\TransactionRepository;
use PHPUnit\Framework\TestCase;

class GetTest extends TestCase
{
    private $mockController;
    
    protected function setUp(): void
    {
        $transaction = $this->createStub(Transaction::class);
        $transaction->method("getEntityId")->willReturn(1);
        $transaction->method("getUserId")->willReturn(1);
        $transaction->method("getTransactionStatus")->willReturn("status");
        $transaction->method("getTransactionFee")->willReturn(1.00);
        $transaction->method("getCreatedAt")->willReturn("123");
        $transaction->method("getUpdatedAt")->willReturn("456");
        $transaction->method("getTransactionProvider")->willReturn("provider");
        $transaction->method("getTransactionAmount")->willReturn(10.00);
        $transaction->method("getTransactionRecipientId")->willReturn(2);
        $transaction->method("getTransactionRecipientName")->willReturn("name");
        $transaction->method("getTransactionCurrency")->willReturn("currency");
        $transaction->method("getTransactionDetails")->willReturn("details");
        
        $map = [
            [1, $transaction],
            [2, null]
        ];
        
        $repository = $this->createStub(TransactionRepository::class);
        $repository->method("getTransactionByEntityId")->will($this->returnValueMap($map));
        
        $this->mockController = $this->getMockBuilder(Get::class)
                                     ->setMethodsExcept(["execute"])
                                     ->setConstructorArgs([new JsonResponse(), $repository])
                                     ->getMock();
    }
    
    protected function tearDown(): void
    {
        $this->mockController = null;
    }
    
    /**
     * @dataProvider executeProvider
     *
     * @param $input
     * @param $expected
     *
     * @runInSeparateProcess
     */
    public function testExecute($input, $expected)
    {
        $this->expectOutputString($expected);
        $this->mockController->execute($input);
    }
    
    public function executeProvider()
    {
        return [
            "missing params" => [
                ["dummy" => "mock"],
                '{"status":"error","errors":[{"code":400,"message":"Missing required argument entity_id."}]}',
            ],
            "success"        => [
                ["entity_id" => 1],
                '{"entity_id":1,"user_id":1,"transaction_status":"status","transaction_fee":1,' .
                '"created_at":"123","updated_at":"456","transaction_provider":"provider","transaction_amount":10,' .
                '"transaction_recipient_id":2,"transaction_recipient_name":"name","transaction_currency":"currency",' .
                '"transaction_details":"details"}',
            ],
            "not found"      => [
                ["entity_id" => 2],
                '{"status":"error","errors":[{"code":404,"message":"Transaction with the id 2 not found"}]}',
            ],
        ];
    }
}

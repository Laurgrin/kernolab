<?php
namespace Test\Unit\Controller\Transaction;

use Kernolab\Controller\JsonResponse;
use Kernolab\Controller\Transaction\Confirm;
use Kernolab\Model\Entity\Transaction\Transaction;
use Kernolab\Model\Entity\Transaction\TransactionRepository;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    private $controller;
    
    protected function setUp(): void
    {
        $transaction = $this->createMock(Transaction::class);
        $transaction->method("getEntityId")
                    ->willReturn(1);
        
        $repository = $this->createMock(TransactionRepository::class);
        $repository->method("confirmTransaction")
                   ->with(1)
                   ->willReturn($transaction);
        
        $this->controller = new Confirm(new JsonResponse(), $repository);
    }
    
    protected function tearDown(): void
    {
        $this->controller = null;
    }
    
    /**
     * @runInSeparateProcess
     * @dataProvider executeProvider
     *
     * @param $input
     * @param $expected
     */
    public function testExecute($input, $expected)
    {
        $this->expectOutputString($expected);
        $this->controller->execute($input);
    }
    
    public function executeProvider()
    {
        return [
            [
                [
                    "entity_id"         => "1",
                    "verification_code" => "111",
                ],
                '{"status":"success","code":"200","message":"Transaction 1 confirmed successfully."}',
            ],
            [
                [
                    "entity_id"         => "1",
                    "verification_code" => "112",
                ],
                '{"status":"error","errors":[{"code":401,"message":"Invalid verification code"}]}',
            ],
            [
                [
                    "entity_id" => "1",
                ],
                '{"status":"error","errors":[{"code":400,"message":"Missing required argument verification_code."}]}',
            ],
        ];
    }
}

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
        
        $map = [
          [1, $transaction],
          [100, null]
        ];
        
        $repository = $this->createMock(TransactionRepository::class);
        $repository->method("confirmTransaction")
                   ->will($this->returnValueMap($map));
        
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
            "success" => [
                [
                    "entity_id"         => "1",
                    "verification_code" => "111",
                ],
                '{"status":"success","code":"200","message":"Transaction 1 confirmed successfully."}',
            ],
            "validation fails" => [
                [
                    "entity_id"         => "1",
                    "verification_code" => "112",
                ],
                '{"status":"error","errors":[{"code":401,"message":"Invalid verification code"}]}',
            ],
            "missing params" => [
                [
                    "entity_id" => "1",
                ],
                '{"status":"error","errors":[{"code":400,"message":"Missing required argument verification_code."}]}',
            ],
            "entity does not exist" => [
                [
                    "entity_id" => "100",
                    "verification_code" => "111",
                ],
                '{"status":"error","errors":[{"code":404,"message":"Transaction ID 100 is already confirmed or does not exist"}]}',
            ],
        ];
    }
}

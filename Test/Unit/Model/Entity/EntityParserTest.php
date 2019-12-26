<?php
namespace Test\Unit\Model\Entity;

use Kernolab\Model\Entity\EntityInterface;
use Kernolab\Model\Entity\Transaction\Transaction;
use Kernolab\Model\Entity\EntityParser;
use PHPUnit\Framework\TestCase;

class EntityParserTest extends TestCase
{
    private $entityParser;
    
    private $transaction;
    
    protected function setUp(): void
    {
        $this->entityParser = new EntityParser();
        $this->transaction  = new Transaction(
            0,
            1,
            "test",
            20,
            "test",
            200,
            2,
            "test",
            "EUR"
        );
        $this->transaction->setUserId(1)
                          ->setTransactionStatus("test")
                          ->setTransactionRecipientName("test")
                          ->setTransactionRecipientId(2)
                          ->setTransactionCurrency("EUR")
                          ->setTransactionAmount(200);
    }
    
    protected function tearDown(): void
    {
        $this->entityParser = null;
    }
    
    /**
     * @dataProvider toSnakeCaseProvider
     * @runInSeparateProcess
     *
     * @param $input
     * @param $expected
     */
    public function testToSnakeCase($input, $expected)
    {
        $this->assertEquals($expected, $this->entityParser->toSnakeCase($input));
    }
    
    /**
     * @runInSeparateProcess
     *
     * @throws \ReflectionException
     */
    public function testGetEntityTarget()
    {
        $this->assertEquals("transaction", $this->entityParser->getEntityTarget($this->transaction));
    }
    
    /**
     * @runInSeparateProcess
     *
     * @throws \ReflectionException
     */
    public function testGetEntityProperties()
    {
        $expected = [
            "user_id"                    => 1,
            "transaction_status"         => "test",
            "transaction_amount"         => 200,
            "transaction_recipient_id"   => 2,
            "transaction_recipient_name" => "test",
            "transaction_currency"       => "EUR",
        ];
        
        $this->assertEquals($expected, $this->entityParser->getEntityProperties($this->transaction));
    }
    
    public function toSnakeCaseProvider()
    {
        return [
            ["transactionId", "transaction_id"],
            ["TransactionId", "transaction_id"],
            ["transActionId", "trans_action_id"],
        ];
    }
}

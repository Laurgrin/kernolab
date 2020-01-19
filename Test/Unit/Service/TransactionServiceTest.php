<?php declare(strict_types = 1);
namespace Unit\Service;

use Kernolab\Exception\HourlyTransactionException;
use Kernolab\Exception\LifetimeTransactionAmountException;
use Kernolab\Exception\TransactionConfirmationException;
use Kernolab\Model\Entity\Transaction\Transaction;
use Kernolab\Model\Entity\Transaction\TransactionRepository;
use Kernolab\Service\TransactionService;
use PHPUnit\Framework\TestCase;

class TransactionServiceTest extends TestCase
{
    private $transactionService;
    
    protected function setUp(): void
    {
        $transactionSuccess   = [];
        $transactionFail      = [];
        $now                   = new \DateTime();
    
        for ($i = 1; $i <= 5; $i++) {
            $transaction = new Transaction();
            $transaction->setCreatedAt($now->format('Y-m-d H:i:s'));
            $transaction->setTransactionAmount(10);
            $transaction->setTransactionCurrency('EUR');
            $transaction->setTransactionFee(1);
            $transactionSuccess[] = $transaction;
        }
    
        for ($i = 1; $i <= 12; $i++) {
            $transaction = new Transaction();
            $transaction->setCreatedAt($now->format('Y-m-d H:i:s'));
            $transaction->setTransactionAmount(100);
            $transaction->setTransactionFee(10);
            $transaction->setTransactionCurrency('EUR');
            $transactionFail[] = $transaction;
        }
        
        $map = [[1, $transactionSuccess], [2, $transactionFail]];
        
        $transactionRepository = $this->createStub(TransactionRepository::class);
        $transactionRepository->method('getTransactionsByUserId')->willReturnMap($map);
        $transactionRepository->method('getTransactionByEntityId')->willReturnMap($map);
        $this->transactionService = new TransactionService($transactionRepository);
    }
    
    protected function tearDown(): void
    {
        $this->transactionService = null;
    }
    
    /**
     * @runInSeparateProcess
     * @dataProvider setTransactionFeeProvider
     *
     * @param $input
     * @param $expected
     *
     * @throws \Kernolab\Exception\ConfigurationFileNotFoundException
     * @throws \Kernolab\Exception\DateTimeException
     * @throws \Kernolab\Exception\MySqlConnectionException
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    public function testSetTransactionFee($input, $expected): void
    {
        $this->transactionService->setTransactionFee($input[0], $input[1]);
        $this->assertEquals($expected, $input[1]['transaction_fee']);
    }
    
    public function setTransactionFeeProvider(): array
    {
        return [
          [
              [
                  1,
                  ['transaction_amount' => 100]
              ],
              10
          ],
          [
              [
                  2,
                  ['transaction_amount' => 100]
              ],
              5
          ],
        ];
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testCheckUserLifetimeTransactionAmount(): void
    {
        $this->assertEquals($this->transactionService, $this->transactionService->checkUserLifetimeTransactionAmount(1));
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testCheckUserLifetimeTransactionAmountException(): void
    {
        $this->expectException(LifetimeTransactionAmountException::class);
        $this->transactionService->checkUserLifetimeTransactionAmount(2);
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testConfirmTransactionException(): void
    {
        $this->expectException(TransactionConfirmationException::class);
        $this->transactionService->confirmTransaction(1, 112);
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testCheckUserTransactionCount(): void
    {
        $this->assertEquals($this->transactionService, $this->transactionService->checkUserTransactionCount(1));
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testCheckUserTransactionCountException(): void
    {
        $this->expectException(HourlyTransactionException::class);
        $this->transactionService->checkUserTransactionCount(2);
    }
}

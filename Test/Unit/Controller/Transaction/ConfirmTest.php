<?php declare(strict_types = 1);

namespace Test\Unit\Controller\Transaction;

use Kernolab\Controller\JsonResponse;
use Kernolab\Controller\Transaction\Confirm;
use Kernolab\Exception\ApiException;
use Kernolab\Exception\EntityNotFoundException;
use Kernolab\Exception\TransactionConfirmationException;

require_once('AbstractTransactionControllerTest.php');

class ConfirmTest extends AbstractTransactionControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $entityNotFoundException          = $this->createStub(EntityNotFoundException::class);
        $transactionConfirmationException = $this->createStub(TransactionConfirmationException::class);
        $transaction = $this->transaction;
        $this->transactionService->method('confirmTransaction')
                           ->willReturnCallback(static function($entityId, $verificationCode)
                           use ($transaction, $entityNotFoundException, $transactionConfirmationException) {
                               if ((int)$entityId !== 1) {
                                   throw $entityNotFoundException;
                               }
            
                               if ((int)$verificationCode !== 111) {
                                   throw $transactionConfirmationException;
                               }
            
                               return $transaction;
                           }
                           );
        
        $this->controller = new Confirm(
            new JsonResponse(),
            $this->requestValidator,
            $this->transactionService,
            $this->exceptionHandler
        );
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecuteSuccess(): void
    {
        $input               = [
            'entity_id'         => '1',
            'verification_code' => '111',
        ];
        $jsonSuccessResponse = new JsonResponse();
        $jsonSuccessResponse->addField('status', 'success')
                            ->addField('code', 200)
                            ->addField('message', 'Transaction 1 confirmed successfully.');
        
        $this->assertEquals($jsonSuccessResponse, $this->controller->execute($input));
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecuteValidationException(): void
    {
        $input = [
            'entity_id'         => '1',
            'verification_code' => '112',
        ];
        
        $this->expectException(ApiException::class);
        $this->controller->execute($input);
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecuteRequestParameterException(): void
    {
        $input = [
            'entity_id'         => '1',
        ];
        
        $this->expectException(ApiException::class);
        $this->controller->execute($input);
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testExecuteEntityNotFoundException(): void
    {
        $input = [
            'entity_id'         => '100',
            'verification_code' => '111',
        ];
        
        $this->expectException(ApiException::class);
        $this->controller->execute($input);
    }
}

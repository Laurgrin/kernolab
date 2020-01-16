<?php declare(strict_types = 1);

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\JsonResponse;

class Confirm extends AbstractTransactionController
{
    /**
     * Process a request and return a response
     *
     * @param array $requestParams
     *
     * @return JsonResponse
     */
    public function execute(array $requestParams): JsonResponse
    {
        $requiredParams = ['entity_id', 'verification_code'];
        if (!$this->validateParams($requestParams, $requiredParams)) {
            return $this->jsonResponse;
        }
        
        $entityId         = $requestParams['entity_id'];
        $verificationCode = $requestParams['verification_code'];
        
        if ($this->validateTransactionConfirmation($entityId, $verificationCode)) {
            $transaction = $this->transactionService->confirmTransaction($entityId);
            if ($transaction) {
                return $this->jsonResponse->addField('status', 'success')
                                        ->addField('code', '200')
                                        ->addField('message', sprintf('Transaction %s confirmed successfully.',
                                                                      $transaction->getEntityId())
                                        );
            }
    
            return $this->jsonResponse->addError(
                404,
                sprintf('Transaction ID %s is already confirmed or does not exist', $entityId)
            );
        }
    
        return $this->jsonResponse->addError(401, 'Invalid verification code');
    }
    
    /**
     * Validate the received verification code.
     *
     * @param $userId
     * @param $verificationCode
     *
     * @return bool
     * @codeCoverageIgnore
     */
    protected function validateTransactionConfirmation($userId, $verificationCode): bool
    {
        return $verificationCode === '111';
    }
}
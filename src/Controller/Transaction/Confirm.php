<?php declare(strict_types = 1);

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\JsonResponse;

class Confirm extends AbstractTransactionController
{
    /**
     * Process a request and return a response
     *
     * @param array $params
     *
     * @return JsonResponse
     */
    public function execute(array $params): JsonResponse
    {
        $requiredParams = ['entity_id', 'verification_code'];
        if (!$this->validateParams($params, $requiredParams)) {
            return $this->jsonResponse;
        }
        
        $entityId         = $params['entity_id'];
        $verificationCode = $params['verification_code'];
        
        if ($this->validateTransactionConfirmation($entityId, $verificationCode)) {
            $transaction = $this->transactionRepository->confirmTransaction($entityId);
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
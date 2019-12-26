<?php

namespace Kernolab\Controller\Transaction;

class Confirm extends AbstractTransactionController
{
    /**
     * Process a request and return a response
     *
     * @param array $params
     *
     * @return mixed
     */
    public function execute(array $params)
    {
        $requiredParams = ["entity_id", "verification_code"];
        if (!$this->validateParams($params, $requiredParams)) {
            echo $this->jsonResponse->getResponse();
            
            return;
        }
        
        $entityId           = $params["entity_id"];
        $verificationCode = $params["verification_code"];
        
        if ($this->validateTransactionConfirmation($entityId, $verificationCode)) {
            $transaction = $this->transactionRepository->confirmTransaction($entityId);
            if ($transaction) {
                echo $this->jsonResponse->addField("status", "success")
                                        ->addField("code", "200")
                                        ->addField("message", "Transaction {$transaction->getEntityId()} confirmed successfully.")
                                        ->getResponse();
            } else {
                echo $this->jsonResponse->addError(
                    404,
                    "Transaction ID {$entityId} is already confirmed or does not exist"
                )->getResponse();
            }
        } else {
            echo $this->jsonResponse->addError(401, "Invalid verification code")->getResponse();
            
            return;
        }
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
        if ($verificationCode === "111") {
            return true;
        }
        
        return false;
    }
}
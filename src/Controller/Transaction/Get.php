<?php

namespace Kernolab\Controller\Transaction;

class Get extends AbstractTransactionController
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
        $requiredParams = ["entity_id"];
        if (!$this->validateParams($params, $requiredParams)) {
            echo $this->jsonResponse->getResponse();
            
            return;
        }
        
        $entity = $this->transactionRepository->getTransactionByEntityId($params["entity_id"]);
        if ($entity) {
            echo $this->jsonResponse->addField("entity_id", $entity->getEntityId())
                                    ->addField("user_id", $entity->getUserId())
                                    ->addField("transaction_status", $entity->getTransactionStatus())
                                    ->addField("transaction_fee", $entity->getTransactionFee())
                                    ->addField("created_at", $entity->getCreatedAt())
                                    ->addField("updated_at", $entity->getUpdatedAt())
                                    ->addField("transaction_provider", $entity->getTransactionProvider())
                                    ->addField("transaction_amount", $entity->getTransactionAmount())
                                    ->addField("transaction_recipient_id", $entity->getTransactionRecipientId())
                                    ->addField("transaction_recipient_name", $entity->getTransactionRecipientName())
                                    ->addField("transaction_currency", $entity->getTransactionCurrency())
                                    ->addField("transaction_details", $entity->getTransactionDetails())
                                    ->getResponse();
        } else {
            echo $this->jsonResponse->addError(
                404,
                "Transaction with the id " . $params["entity_id"] . " not found"
            )->getResponse();
        }
        
        return;
    }
}
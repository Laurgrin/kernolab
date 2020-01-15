<?php declare(strict_types = 1);

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\JsonResponse;

class Get extends AbstractTransactionController
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
        $requiredParams = ['entity_id'];
        if (!$this->validateParams($params, $requiredParams)) {
            return $this->jsonResponse;
        }
        
        $entity = $this->transactionRepository->getTransactionByEntityId($params['entity_id']);
        if ($entity) {
            return $this->jsonResponse->addField('entity_id', $entity->getEntityId())
                                    ->addField('user_id', $entity->getUserId())
                                    ->addField('transaction_status', $entity->getTransactionStatus())
                                    ->addField('transaction_fee', $entity->getTransactionFee())
                                    ->addField('created_at', $entity->getCreatedAt())
                                    ->addField('updated_at', $entity->getUpdatedAt())
                                    ->addField('transaction_provider', $entity->getTransactionProvider())
                                    ->addField('transaction_amount', $entity->getTransactionAmount())
                                    ->addField('transaction_recipient_id', $entity->getTransactionRecipientId())
                                    ->addField('transaction_recipient_name', $entity->getTransactionRecipientName())
                                    ->addField('transaction_currency', $entity->getTransactionCurrency())
                                    ->addField('transaction_details', $entity->getTransactionDetails());
        }
    
        return $this->jsonResponse->addError(
            404,
            sprintf('Transaction with the id %s not found', $params['entity_id'])
        );
    }
}
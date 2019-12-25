<?php

namespace Kernolab\Model\Entity\Transaction;

use Kernolab\Model\DataSource\Criteria;
use Kernolab\Model\DataSource\DataSourceInterface;

class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * @var \Kernolab\Model\DataSource\DataSourceInterface
     */
    protected $dataSource;
    
    /**
     * @var \Kernolab\Model\Entity\Transaction\TransactionProviderRuleInterface
     */
    protected $transactionProviderRule;
    
    /**
     * TransactionRepository constructor.
     *
     * @param \Kernolab\Model\DataSource\DataSourceInterface                      $dataSource
     * @param \Kernolab\Model\Entity\Transaction\TransactionProviderRuleInterface $transactionProviderRule
     */
    public function __construct(
        DataSourceInterface $dataSource,
        TransactionProviderRuleInterface $transactionProviderRule = null
    ) {
        $this->dataSource              = $dataSource;
        $this->transactionProviderRule = $transactionProviderRule;
    }
    
    /**
     * Save a new transaction to persistent storage.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function createTransaction(array $params)
    {
        $params = $this->transactionProviderRule->applyProviderRules($params);
        
        $transaction = new Transaction();
        $transaction->setUserId($params["user_id"])
                    ->setTransactionStatus("awaiting_confirmation")
                    ->setTransactionRecipientName($params["transaction_recipient_name"])
                    ->setTransactionRecipientId($params["transaction_recipient_id"])
                    ->setTransactionCurrency($params["transaction_currency"])
                    ->setTransactionAmount($params["transaction_amount"])
                    ->setTransactionDetails($params["transaction_details"])
                    ->setTransactionFee($params["transaction_fee"])
                    ->setTransactionProvider($params["transaction_provider"]);
        
        return $this->dataSource->set([$transaction])[0];
    }
    
    /**
     * Get all transactions made by a specific user ID.
     *
     * @param int $userId
     *
     * @return mixed
     */
    public function getTransactionsByUserId(int $userId)
    {
        $criteria = new Criteria("user_id", "eq", $userId);
        
        return $this->dataSource->get([$criteria], "transaction");
    }
    
    /**
     * Confirms a transaction.
     *
     * @param int $entityId
     *
     * @return mixed
     */
    public function confirmTransaction(int $entityId)
    {
        $transaction = new Transaction();
        $transaction->setEntityId($entityId);
        $transaction->setTransactionStatus("confirmed");
        
        return $this->dataSource->set([$transaction])[0];
    }
}
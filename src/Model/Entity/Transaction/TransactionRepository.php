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
     * @return \Kernolab\Model\Entity\Transaction\Transaction
     */
    public function createTransaction(array $params): Transaction
    {
        $params = $this->transactionProviderRule->applyProviderRules($params);
        
        $transaction = $this->createTransactionObject($params);
        $result      = $this->dataSource->set($transaction);
        
        return $result;
    }
    
    /**
     * Get all transactions made by a specific user ID.
     *
     * @param int $userId
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction[]
     */
    public function getTransactionsByUserId(int $userId): array
    {
        $criteria = new Criteria("user_id", "eq", $userId);
        
        $transactions = [];
        foreach ($this->dataSource->get([$criteria], "transaction") as $data) {
            $transactions[] = $this->createTransactionObject($data);
        }
        
        return $transactions;
    }
    
    /**
     * Confirms a transaction.
     *
     * @param int $entityId
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction
     */
    public function confirmTransaction(int $entityId): ?Transaction
    {
        $transaction = $this->getTransactionByEntityId($entityId);
        if ($transaction && $transaction->getTransactionStatus() == "created") {
            $transaction->setTransactionStatus("confirmed");
    
    
            return $this->dataSource->set($transaction);
        }
        
        return null;
    }
    
    /**
     * Gets a specific transaction by transaction ID.
     *
     * @param int $entityId
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction
     */
    public function getTransactionByEntityId(int $entityId): ?Transaction
    {
        $transaction = null;
        $criteria    = new Criteria("entity_id", "eq", $entityId);
        $entityData  = $this->dataSource->get([$criteria], "transaction");
        
        return $this->createTransactionObject($entityData);
    }
    
    /**
     * Processes unprocessed transactions.
     *
     * @param int $limit
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction[]
     */
    public function processTransactions(int $limit = 0): array
    {
        $criteria[]            = new Criteria("transaction_status", "eq", "confirmed");
        $entityData            = $this->dataSource->get($criteria, "transaction");
        $count                 = 0;
        $updatedTransactions = [];
        
        foreach ($entityData as $entity) {
            if ($limit != 0 && $count >= $limit) {
                break;
            }
            $count++;
            
            $transaction = new Transaction();
            $transaction->setEntityId($entity["entity_id"])
                        ->setTransactionStatus("processed");
            $updatedTransactions[] = $this->dataSource->set($transaction);
        }
        
        return $updatedTransactions;
    }
    
    /**
     * Creates a transaction object from database data
     *
     * @param array $data
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction
     */
    protected function createTransactionObject(array $data): ?Transaction
    {
        $transaction = null;
        
        if (!empty($data)) {
            $transaction = new Transaction();
            $transaction->setEntityId($data["entity_id"] ?? 0)
                        ->setUserId($data["user_id"])
                        ->setTransactionStatus($data["transaction_status"])
                        ->setTransactionFee($data["transaction_fee"])
                        ->setCreatedAt($data["created_at"] ?? "")
                        ->setUpdatedAt($data["updated_at"] ?? "")
                        ->setTransactionProvider($data["transaction_provider"])
                        ->setTransactionAmount($data["transaction_amount"])
                        ->setTransactionRecipientId($data["transaction_recipient_id"])
                        ->setTransactionRecipientName($data["transaction_recipient_name"])
                        ->setTransactionCurrency($data["transaction_currency"])
                        ->setTransactionDetails($data["transaction_details"]);
        }
        
        return $transaction;
    }
}
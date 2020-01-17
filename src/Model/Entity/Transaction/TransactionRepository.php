<?php declare(strict_types = 1);

namespace Kernolab\Model\Entity\Transaction;

use Kernolab\Exception\TransactionCreationException;
use Kernolab\Model\DataSource\Criteria;
use Kernolab\Model\DataSource\DataSourceInterface;
use Kernolab\Model\Entity\EntityInterface;

class TransactionRepository implements TransactionRepositoryInterface
{
    public const    STATUS_CREATED   = 'created';
    public const    STATUS_CONFIRMED = 'confirmed';
    public const    STATUS_PROCESSED = 'processed';
    protected const ENTITY_TABLE     = 'transaction';
    
    /**
     * @var \Kernolab\Model\DataSource\MySql\DataSource
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
     * @throws \Kernolab\Exception\TransactionCreationException
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    public function createTransaction(array $params): ?EntityInterface
    {
        $params = $this->transactionProviderRule->applyProviderRules($params);
        
        $transaction = $this->createTransactionObject($params);
        
        if ($transaction === null) {
            throw new TransactionCreationException('There was an error while creating the transaction object.');
        }
        
        return $this->dataSource->set($transaction);
    }
    
    /**
     * Get all transactions made by a specific user ID.
     *
     * @param int $userId
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction[]
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    public function getTransactionsByUserId(int $userId): array
    {
        $criteria = new Criteria('user_id', 'eq', $userId);
        
        $transactions = [];
        foreach ($this->dataSource->get([$criteria], self::ENTITY_TABLE) as $data) {
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
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    public function confirmTransaction(int $entityId): EntityInterface
    {
        $transaction = $this->getTransactionByEntityId($entityId);
        if ($transaction && $transaction->getTransactionStatus() === 'created') {
            $transaction->setTransactionStatus(self::STATUS_CONFIRMED);
            
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
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    public function getTransactionByEntityId(int $entityId): ?EntityInterface
    {
        $transaction = null;
        $criteria    = new Criteria('entity_id', 'eq', $entityId);
        $entityData  = $this->dataSource->get([$criteria], 'transaction');
        
        return $this->createTransactionObject($entityData);
    }
    
    /**
     * Processes unprocessed transactions.
     *
     * @param int $limit
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction[]
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    public function processTransactions(int $limit = 0): array
    {
        $criteria[]          = new Criteria('transaction_status', 'eq', self::STATUS_CONFIRMED);
        $entityData          = $this->dataSource->get($criteria, 'transaction');
        $count               = 0;
        $updatedTransactions = [];
        
        foreach ($entityData as $entity) {
            if ($limit !== 0 && $count >= $limit) {
                break;
            }
            $count++;
            
            $transaction = new Transaction();
            $transaction->setEntityId($entity['entity_id'])
                        ->setTransactionStatus(self::STATUS_PROCESSED);
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
            $transaction->setEntityId(array_key_exists('entity_id', $data) ? (int)$data['entity_id'] : 0)
                        ->setUserId((int)$data['user_id'])
                        ->setTransactionStatus($data['transaction_status'])
                        ->setTransactionFee((float)$data['transaction_fee'])
                        ->setCreatedAt($data['created_at'] ?? '')
                        ->setUpdatedAt($data['updated_at'] ?? '')
                        ->setTransactionProvider($data['transaction_provider'])
                        ->setTransactionAmount((float)$data['transaction_amount'])
                        ->setTransactionRecipientId((int)$data['transaction_recipient_id'])
                        ->setTransactionRecipientName($data['transaction_recipient_name'])
                        ->setTransactionCurrency($data['transaction_currency'])
                        ->setTransactionDetails($data['transaction_details']);
        }
        
        return $transaction;
    }
}
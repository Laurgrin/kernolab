<?php

namespace Kernolab\Model\Entity\Transaction;

use Kernolab\Model\DataSource\Criteria;
use Kernolab\Model\DataSource\DataSourceInterface;

class TransactionRepository implements TransactionRepositoryInterface
{
    const DATETIME_FORMAT = "Y-m-d H:i:s";
    
    /**
     * @var \Kernolab\Model\DataSource\DataSourceInterface
     */
    protected $dataSource;
    
    /**
     * TransactionRepository constructor.
     *
     * @param \Kernolab\Model\DataSource\DataSourceInterface $dataSource
     */
    public function __construct(DataSourceInterface $dataSource)
    {
        $this->dataSource = $dataSource;
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
        $transaction = new Transaction();
        $transaction->setUserId($params["user_id"])
                    ->setTransactionStatus("awaiting_confirmation")
                    ->setTransactionRecipientName($params["transaction_recipient_name"])
                    ->setTransactionRecipientId($params["transaction_recipient_id"])
                    ->setTransactionCurrency($params["transaction_currency"])
                    ->setTransactionAmount($params["transaction_amount"])
                    ->setTransactionDetails($params["transaction_details"]);
        
        return $this->dataSource->set([$transaction])[0];
    }
    
    /**
     * Update an array of transactions on the persistent storage.
     *
     * @param Transaction[] $transactions
     *
     * @return mixed
     */
    public function updateTransactions(array $transactions)
    {
        // TODO: Implement updateTransactions() method.
    }
    
    /**
     * Get the transaction count of the user in the past hour.
     *
     * @param int $userId
     *
     * @return int
     * @throws \Exception
     */
    public function getTransactionCount(int $userId): int
    {
        $count    = 0;
        $criteria = new Criteria("user_id", "eq", $userId);
        $dataset  = $this->dataSource->get([$criteria], "transaction");
        $now      = new \DateTime();
        
        foreach ($dataset as $entityData) {
            $transactionTime = \DateTime::createFromFormat(self::DATETIME_FORMAT, $entityData["created_at"]);
            $timeDifference  = $now->diff($transactionTime);
            
            /* We have to check all time units above hours as well, since the next year at the same hour would make the
            hour difference 0 */
            if ($timeDifference->y > 0 ||
                $timeDifference->m > 0 ||
                $timeDifference->d > 0 ||
                $timeDifference->h >= 1
            ) {
                $count++;
            }
        }
        
        return $count;
    }
}
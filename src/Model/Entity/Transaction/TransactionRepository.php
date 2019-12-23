<?php

namespace Kernolab\Model\Entity\Transaction;

use Kernolab\Model\DataSource\DataSourceInterface;

class TransactionRepository implements TransactionRepositoryInterface
{
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
     * @param \Kernolab\Model\Entity\Transaction\Transaction $transaction
     *
     * @return mixed
     */
    public function createTransaction(Transaction $transaction)
    {
        // TODO: Implement createTransaction() method.
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
}
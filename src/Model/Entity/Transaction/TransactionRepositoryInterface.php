<?php

namespace Kernolab\Model\DataSource\MySql;

use Kernolab\Model\Entity\Transaction\Transaction;

interface TransactionRepositoryInterface
{
    /**
     * Save a new transaction to persistent storage.
     *
     * @param \Kernolab\Model\Entity\Transaction\Transaction $transaction
     *
     * @return mixed
     */
    public function createTransaction(Transaction $transaction);
    
    /**
     * Update an array of transactions on the persistent storage.
     *
     * @param Transaction[] $transactions
     *
     * @return mixed
     */
    public function updateTransactions(array $transactions);
}
<?php

namespace Kernolab\Model\Entity\Transaction;

interface TransactionRepositoryInterface
{
    /**
     * Save a new transaction to persistent storage.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function createTransaction(array $params);
    
    /**
     * Update an array of transactions on the persistent storage.
     *
     * @param Transaction[] $transactions
     *
     * @return mixed
     */
    public function updateTransactions(array $transactions);
    
    /**
     * Get the transaction count of the user in the past hour.
     *
     * @param int $userId
     *
     * @return int
     */
    public function getTransactionCount(int $userId);
}
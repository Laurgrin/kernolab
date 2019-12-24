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
     * Get all transactions made by a specific user ID.
     *
     * @param int $userId
     *
     * @return mixed
     */
    public function getTransactionsByUserId(int $userId);
}
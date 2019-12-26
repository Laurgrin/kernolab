<?php

namespace Kernolab\Model\Entity\Transaction;

interface TransactionRepositoryInterface
{
    /**
     * Save a new transaction to persistent storage.
     *
     * @param array $params
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction
     */
    public function createTransaction(array $params): Transaction;
    
    /**
     * Get all transactions made by a specific user ID.
     *
     * @param int $userId
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction[]
     */
    public function getTransactionsByUserId(int $userId): array;
    
    /**
     * Confirms a transaction.
     *
     * @param int $entityId
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction
     */
    public function confirmTransaction(int $entityId): ?Transaction;
    
    /**
     * Gets a specific transaction by transaction ID.
     *
     * @param int $entityId
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction
     */
    public function getTransactionByEntityId(int $entityId): ?Transaction;
    
    /**
     * Processes unprocessed transactions up to the $limit. No limit if 0.
     *
     * @param int $limit
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction[]
     */
    public function processTransactions(int $limit = 0): array;
}
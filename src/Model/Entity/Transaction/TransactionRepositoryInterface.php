<?php declare(strict_types = 1);

namespace Kernolab\Model\Entity\Transaction;

use Kernolab\Model\Entity\EntityInterface;

interface TransactionRepositoryInterface
{
    /**
     * Save a new transaction to persistent storage.
     *
     * @param array $params
     *
     * @return EntityInterface
     * @throws \Kernolab\Exception\TransactionCreationException
     */
    public function createTransaction(array $params): ?EntityInterface;
    
    /**
     * Get all transactions made by a specific user ID.
     *
     * @param int $userId
     *
     * @return EntityInterface[]
     */
    public function getTransactionsByUserId(int $userId): array;
    
    /**
     * Confirms a transaction.
     *
     * @param int $entityId
     *
     * @return EntityInterface
     */
    public function confirmTransaction(int $entityId): EntityInterface;
    
    /**
     * Gets a specific transaction by transaction ID.
     *
     * @param int $entityId
     *
     * @return EntityInterface
     */
    public function getTransactionByEntityId(int $entityId): ?EntityInterface;
    
    /**
     * Processes unprocessed transactions up to the $limit. No limit if 0.
     *
     * @param int $limit
     *
     * @return EntityInterface[]
     */
    public function processTransactions(int $limit = 0): array;
}
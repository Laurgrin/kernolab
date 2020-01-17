<?php declare(strict_types = 1);

namespace Kernolab\Service;

use Kernolab\Exception\DateTimeException;
use Kernolab\Exception\HourlyTransactionException;
use Kernolab\Exception\LifetimeTransactionAmountException;
use Kernolab\Exception\TransactionConfirmationException;
use Kernolab\Model\Entity\EntityInterface;
use Kernolab\Model\Entity\Transaction\Transaction;
use Kernolab\Model\Entity\Transaction\TransactionRepositoryInterface;

class TransactionService
{
    protected const DATETIME_FORMAT          = 'Y-m-d H:i:s';
    protected const HOURLY_TRANSACTION_LIMIT = 10;
    
    /**
     * @var \Kernolab\Model\Entity\Transaction\TransactionRepository
     */
    protected $transactionRepository;
    
    /**
     * TransactionService constructor.
     *
     * @param \Kernolab\Model\Entity\Transaction\TransactionRepositoryInterface $transactionRepository
     */
    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }
    
    /**
     * Checks the user transaction count in the past past hour. Throws an exception if it exceeds the limit.
     *
     * @param int $userId
     *
     * @return TransactionService
     * @throws \Kernolab\Exception\DateTimeException
     * @throws \Kernolab\Exception\HourlyTransactionException
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     * @throws \Kernolab\Exception\MySqlConnectionException
     * @throws \Kernolab\Exception\ConfigurationFileNotFoundException
     */
    public function checkUserTransactionCount(int $userId): TransactionService
    {
        $count        = 0;
        $transactions = $this->transactionRepository->getTransactionsByUserId($userId);
        try {
            $now = new \DateTime();
        } catch (\Exception $e) {
            /* Rethrow into non-generic exception */
            throw new DateTimeException($e->getMessage());
        }
        
        foreach ($transactions as $transaction) {
            $transactionTime = \DateTime::createFromFormat(self::DATETIME_FORMAT, $transaction->getCreatedAt());
            $timeDifference  = $now->diff($transactionTime);
            
            /* We have to check all time units above hours as well, since the next year at the same hour would make the
            hour difference 0 */
            if ($timeDifference->y > 0 || $timeDifference->m > 0 || $timeDifference->d > 0 || $timeDifference->h >= 1) {
                $count++;
            }
        }
        
        if ($count >= self::HOURLY_TRANSACTION_LIMIT) {
            throw new HourlyTransactionException('Hourly transaction count exceeded for this user.');
        }
        
        return $this;
    }
    
    /**
     * Sum lifetime totals of an user and check if another transaction can be created. If any one currency exceeds
     * 1000, no more transactions can be created by that user.
     *
     * @param int $userId
     *
     * @return TransactionService
     * @throws \Kernolab\Exception\LifetimeTransactionAmountException
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     * @throws \Kernolab\Exception\MySqlConnectionException
     * @throws \Kernolab\Exception\ConfigurationFileNotFoundException
     */
    public function checkUserLifetimeTransactionAmount(int $userId): TransactionService
    {
        /** @var \Kernolab\Model\Entity\Transaction\Transaction[] $transactions */
        $transactions      = $this->transactionRepository->getTransactionsByUserId($userId);
        $amountsByCurrency = [];
        
        foreach ($transactions as $transaction) {
            if (!array_key_exists($transaction->getTransactionCurrency(), $amountsByCurrency)) {
                $amountsByCurrency[$transaction->getTransactionCurrency()] = 0;
            }
            $amountsByCurrency[$transaction->getTransactionCurrency()] += ($transaction->getTransactionAmount() + $transaction->getTransactionFee());
            if ($amountsByCurrency[$transaction->getTransactionCurrency()] >= 1000) {
                throw new LifetimeTransactionAmountException('Lifetime transaction amount exceeded for this user.');
            }
        }
        
        return $this;
    }
    
    /**
     * Gets the transaction fee to be applied to the transaction and adds it to the request params array.
     *
     * @param int   $userId
     * @param array $requestParams
     *
     * @return TransactionService
     * @throws \Kernolab\Exception\DateTimeException
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     * @throws \Kernolab\Exception\MySqlConnectionException
     * @throws \Kernolab\Exception\ConfigurationFileNotFoundException
     */
    public function setTransactionFee(int $userId, array &$requestParams): TransactionService
    {
        /** @var \Kernolab\Model\Entity\Transaction\Transaction[] $transactions */
        $transactions = $this->transactionRepository->getTransactionsByUserId($userId);
        $transactionAmount = $requestParams['transaction_amount'];
        try {
            $now = new \DateTime();
        } catch (\Exception $e) {
            throw new DateTimeException($e->getMessage());
        }
    
        /* Filters out the transactions not made today */
        $dailyTransactions = array_filter($transactions, static function($transaction) use ($now) {
            /** @var \Kernolab\Model\Entity\Transaction\Transaction $transaction */
            $transactionTime = \DateTime::createFromFormat(self::DATETIME_FORMAT, $transaction->getCreatedAt());
            $timeDifference  = $now->diff($transactionTime);
            
            return !($timeDifference->y > 0 || $timeDifference->m > 0 || $timeDifference->d >= 1);
        }
        );
        
        /* Sum amounts by currency */
        $amountsByCurrency = [];
        foreach ($dailyTransactions as $dailyTransaction) {
            if (!array_key_exists($dailyTransaction->getTransactionCurrency(), $amountsByCurrency)) {
                $amountsByCurrency[$dailyTransaction->getTransactionCurrency()] = 0;
            }
            
            $amountsByCurrency[$dailyTransaction->getTransactionCurrency()] += $dailyTransaction->getTransactionAmount();
            if ($amountsByCurrency[$dailyTransaction->getTransactionCurrency()] >= 100) {
                $requestParams['transaction_fee'] = $transactionAmount * 0.05;
                return $this;
            }
        }
    
        $requestParams['transaction_fee'] = $transactionAmount * 0.1;
        return $this;
    }
    
    /**
     * Creates a transaction using request params and returns the created entity model.
     *
     * @param array $requestParams
     *
     * @return \Kernolab\Model\Entity\EntityInterface
     * @throws \Kernolab\Exception\TransactionCreationException
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     * @throws \Kernolab\Exception\MySqlConnectionException
     * @throws \Kernolab\Exception\ConfigurationFileNotFoundException
     */
    public function createTransaction(array $requestParams): EntityInterface
    {
        $requestParams['transaction_status'] = 'created';
        
        return $this->transactionRepository->createTransaction($requestParams);
    }
    
    /**
     * Confirms a transaction by it's entity ID. Throws an error if 2FA code does not match.
     *
     * @param int $entityId
     * @param int $verificationCode
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     * @throws \Kernolab\Exception\TransactionConfirmationException
     * @throws \Kernolab\Exception\EntityNotFoundException
     * @throws \Kernolab\Exception\MySqlConnectionException
     * @throws \Kernolab\Exception\ConfigurationFileNotFoundException
     */
    public function confirmTransaction(int $entityId, int $verificationCode): Transaction
    {
        if ($verificationCode !== 111) {
            throw new TransactionConfirmationException(
                '2FA verification failed while attempting to confirm the transaction.'
            );
        }
        
        return $this->transactionRepository->confirmTransaction($entityId);
    }
    
    /**
     * @param int $entityId
     *
     * @return \Kernolab\Model\Entity\Transaction\Transaction
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     * @throws \Kernolab\Exception\EntityNotFoundException
     * @throws \Kernolab\Exception\MySqlConnectionException
     * @throws \Kernolab\Exception\ConfigurationFileNotFoundException
     */
    public function getTransactionByEntityId(int $entityId): Transaction
    {
        return $this->transactionRepository->getTransactionByEntityId($entityId);
    }
}
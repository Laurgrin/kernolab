<?php

namespace Kernolab\Controller\Transaction;

class Create extends AbstractTransactionController
{
    const DATETIME_FORMAT = "Y-m-d H:i:s";
    
    /**
     * Process a request and return a response
     *
     * @param array $params
     *
     * @return void
     */
    public function execute(array $params)
    {
        $requiredKeys = [
            "user_id",
            "transaction_details",
            "transaction_recipient_id",
            "transaction_recipient_name",
            "transaction_amount",
            "transaction_currency",
        ];
        
        if (!$this->validateParams($params, $requiredKeys)) {
            echo $this->jsonResponse->getResponse();
            
            return;
        }
        
        try {
            $userId = $params["user_id"];
            
            if ($this->getTransactionCount($userId) < 10) {
                if ($this->canTransfer($userId)) {
                    $params["transaction_fee"] = $this->getTransactionFee($userId, $params["transaction_amount"]);
                    $params["transaction_status"] = "created";
                    
                    $entity = $this->transactionRepository->createTransaction($params);
                    echo $this->jsonResponse->addField("status", "success")
                                            ->addField("code", 200)
                                            ->addField("message", "Transaction created successfully.")
                                            ->addField("entity_id", $entity->getEntityId())
                                            ->getResponse();
                    
                    return;
                } else {
                    echo $this->jsonResponse->addError("403", "Maximum lifetime transactions reached.")->getResponse();
                }
            } else {
                echo $this->jsonResponse->addError("403", "Hourly transaction limit exceeded.")->getResponse();
            }
        } catch (\Exception $e) {
            echo $this->jsonResponse->addError("500", $e->getMessage())->getResponse();
        }
    }
    
    /**
     * Get the transaction count of the user in the past hour.
     *
     * @param int $userId
     *
     * @return int
     * @throws \Exception
     * @codeCoverageIgnore
     */
    protected function getTransactionCount(int $userId): int
    {
        $count   = 0;
        $transactions = $this->transactionRepository->getTransactionsByUserId($userId);
        $now     = new \DateTime();
        
        foreach ($transactions as $transaction) {
            $transactionTime = \DateTime::createFromFormat(self::DATETIME_FORMAT, $transaction->getCreatedAt());
            $timeDifference  = $now->diff($transactionTime);
            
            /* We have to check all time units above hours as well, since the next year at the same hour would make the
            hour difference 0 */
            if ($timeDifference->y > 0 || $timeDifference->m > 0 || $timeDifference->d > 0 || $timeDifference->h >= 1) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Gets the transaction fee to be applied to the transaction
     *
     * @param int   $userId
     * @param float $transactionAmount
     *
     * @return float
     * @throws \Exception
     * @codeCoverageIgnore
     */
    protected function getTransactionFee(int $userId, float $transactionAmount): float
    {
        $transactions = $this->transactionRepository->getTransactionsByUserId($userId);
        $now     = new \DateTime();
        
        /* Filters out the transactions not made today */
        $dailyTransactions = array_filter($transactions, function($transaction) use ($now) {
            /** @var \Kernolab\Model\Entity\Transaction\Transaction $transaction*/
            $transactionTime = \DateTime::createFromFormat(self::DATETIME_FORMAT, $transaction->getCreatedAt());
            $timeDifference  = $now->diff($transactionTime);
            
            if ($timeDifference->y > 0 || $timeDifference->m > 0 || $timeDifference->d >= 1) {
                return false;
            }
            
            return true;
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
                return $transactionAmount * 0.05;
            }
        }
    
        return $transactionAmount * 0.1;
    }
    
    /**
     * Sum lifetime totals of an user and check if another transaction can be created. If any one currency exceeds
     * 1000, no more transactions can be created by that user.
     *
     * @param int $userId
     *
     * @return bool
     * @codeCoverageIgnore
     */
    protected function canTransfer(int $userId): bool
    {
        $transactions           = $this->transactionRepository->getTransactionsByUserId($userId);
        $amountsByCurrency = [];
        
        foreach ($transactions as $transaction) {
            if (!array_key_exists($transaction->getTransactionCurrency(), $amountsByCurrency)) {
                $amountsByCurrency[$transaction->getTransactionCurrency()] = 0;
            }
            $amountsByCurrency[$transaction->getTransactionCurrency()] += ($transaction->getTransactionAmount() + $transaction->getTransactionFee());
            if ($amountsByCurrency[$transaction->getTransactionCurrency()] >= 1000) {
                return false;
            }
        }
        
        return true;
    }
}
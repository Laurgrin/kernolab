<?php

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\AbstractController;
use Kernolab\Controller\JsonResponseInterface;
use Kernolab\Exception\MySqlConnectionException;
use Kernolab\Model\DataSource\MySql\DataSource;
use Kernolab\Model\DataSource\MySql\QueryGenerator;
use Kernolab\Model\Entity\EntityParser;
use Kernolab\Model\Entity\Transaction\TransactionProviderRule;
use Kernolab\Model\Entity\Transaction\TransactionRepository;

class Create extends AbstractController
{
    const DATETIME_FORMAT = "Y-m-d H:i:s";
    
    protected $transactionRepository;
    
    public function __construct(JsonResponseInterface $jsonResponse)
    {
        parent::__construct($jsonResponse);
        $queryGenerator = new QueryGenerator();
        $entityParser   = new EntityParser();
        try {
            $dataSource                  = new DataSource($queryGenerator, $entityParser);
            $transactionProviderRule     = new TransactionProviderRule();
            $this->transactionRepository = new TransactionRepository($dataSource, $transactionProviderRule);
        } catch (MySqlConnectionException $e) {
            echo $this->jsonResponse->addError("500", "There were problems getting a response.")->getResponse();
        }
    }
    
    /**
     * Process a request and return a response
     *
     * @param array $params
     *
     * @return void
     */
    public function execute(array $params)
    {
        if (!$this->validateParams($params)) {
            echo $this->jsonResponse->getResponse();
            
            return;
        }
        
        try {
            $userId = $params["user_id"];
            
            if ($this->getTransactionCount($userId) < 10) {
                if ($this->canTransfer($userId)) {
                    $params["transaction_fee"] = $this->getTransactionFee($userId, $params["transaction_amount"]);
                    
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
     * Validates if all the required request params are there. Does not care about their values though.
     *
     * @param array $params
     *
     * @return bool
     */
    protected function validateParams(array $params): bool
    {
        $requestKeys  = array_keys($params);
        $requiredKeys = [
            "user_id",
            "transaction_details",
            "transaction_recipient_id",
            "transaction_recipient_name",
            "transaction_amount",
            "transaction_currency",
        ];
        
        if (array_intersect($requestKeys, $requiredKeys) === $requiredKeys) {
            return true;
        }
        
        $missingParams = array_diff($requiredKeys, $requestKeys);
        foreach ($missingParams as $missingParam) {
            $this->jsonResponse->addError(400, "Missing required argument {$missingParam}.");
        }
        
        return false;
    }
    
    /**
     * Get the transaction count of the user in the past hour.
     *
     * @param int $userId
     *
     * @return int
     * @throws \Exception
     */
    protected function getTransactionCount(int $userId): int
    {
        $count   = 0;
        $dataset = $this->transactionRepository->getTransactionsByUserId($userId);
        $now     = new \DateTime();
        
        foreach ($dataset as $entityData) {
            $transactionTime = \DateTime::createFromFormat(self::DATETIME_FORMAT, $entityData["created_at"]);
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
     */
    protected function getTransactionFee(int $userId, float $transactionAmount): float
    {
        $dataset = $this->transactionRepository->getTransactionsByUserId($userId);
        $now     = new \DateTime();
        
        /* Filters out the transactions not made today */
        $dailyTransactions = array_filter($dataset, function($entityData) use ($now) {
            $transactionTime = \DateTime::createFromFormat(self::DATETIME_FORMAT, $entityData["created_at"]);
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
            if (!array_key_exists($dailyTransaction["transaction_currency"], $amountsByCurrency)) {
                $amountsByCurrency[$dailyTransaction["transaction_currency"]] = 0;
            }
            $amountsByCurrency[$dailyTransaction["transaction_currency"]] += $dailyTransaction["transaction_amount"];
            if ($amountsByCurrency[$dailyTransaction["transaction_currency"]] >= 100) {
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
     */
    protected function canTransfer(int $userId): bool
    {
        $dataset           = $this->transactionRepository->getTransactionsByUserId($userId);
        $amountsByCurrency = [];
        
        foreach ($dataset as $data) {
            if (!array_key_exists($data["transaction_currency"], $amountsByCurrency)) {
                $amountsByCurrency[$data["transaction_currency"]] = 0;
            }
            $amountsByCurrency[$data["transaction_currency"]] += ($data["transaction_amount"] + $data["transaction_fee"]);
            if ($amountsByCurrency[$data["transaction_currency"]] >= 1000) {
                return false;
            }
        }
        
        return true;
    }
}
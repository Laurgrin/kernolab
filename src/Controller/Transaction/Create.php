<?php

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\AbstractController;
use Kernolab\Controller\JsonResponseInterface;
use Kernolab\Exception\MySqlConnectionException;
use Kernolab\Model\DataSource\MySql\DataSource;
use Kernolab\Model\DataSource\MySql\QueryGenerator;
use Kernolab\Model\Entity\EntityParser;
use Kernolab\Model\Entity\Transaction\TransactionRepository;

class Create extends AbstractController
{
    protected $transactionRepository;
    
    public function __construct(JsonResponseInterface $jsonResponse)
    {
        parent::__construct($jsonResponse);
        $queryGenerator = new QueryGenerator();
        $entityParser = new EntityParser();
        try {
            $dataSource = new DataSource($queryGenerator, $entityParser);
            $this->transactionRepository = new TransactionRepository($dataSource);
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
        $requestKeys = array_keys($params);
        $requiredKeys = [
            "user_id",
            "transaction_details",
            "transaction_recipient_id",
            "transaction_recipient_name",
            "transaction_amount",
            "transaction_currency"
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
}
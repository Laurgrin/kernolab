<?php

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\AbstractController;
use Kernolab\Controller\JsonResponseInterface;
use Kernolab\Exception\MySqlConnectionException;
use Kernolab\Model\DataSource\MySql\DataSource;
use Kernolab\Model\DataSource\MySql\QueryGenerator;
use Kernolab\Model\Entity\EntityParser;
use Kernolab\Model\Entity\Transaction\TransactionRepository;

class Confirm extends AbstractController
{
    /**
     * @var \Kernolab\Model\Entity\Transaction\TransactionRepository
     */
    protected $transactionRepository;
    
    /**
     * Confirm constructor.
     *
     * @param JsonResponseInterface $jsonResponse
     */
    public function __construct(JsonResponseInterface $jsonResponse)
    {
        parent::__construct($jsonResponse);
        $queryGenerator = new QueryGenerator();
        $entityParser   = new EntityParser();
        try {
            $dataSource                  = new DataSource($queryGenerator, $entityParser);
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
     * @return mixed
     */
    public function execute(array $params)
    {
        $requiredParams = ["entity_id", "verification_code"];
        if (!$this->validateParams($params, $requiredParams)) {
            echo $this->jsonResponse->getResponse();
            
            return;
        }
        
        $userId           = $params["entity_id"];
        $verificationCode = $params["verification_code"];
        
        if ($this->validateTransactionConfirmation($userId, $verificationCode)) {
            $transaction = $this->transactionRepository->confirmTransaction($userId);
            echo $this->jsonResponse->addField("status", "success")
                                    ->addField("code", "200")
                                    ->addField("message", "Transaction {$transaction->getEntityId()} confirmed successfully.")
                                    ->getResponse();
        } else {
            echo $this->jsonResponse->addError(401, "Invalid verification code")->getResponse();
            
            return;
        }
    }
    
    /**
     * Validate the received verification code.
     *
     * @param $userId
     * @param $verificationCode
     *
     * @return bool
     */
    protected function validateTransactionConfirmation($userId, $verificationCode): bool
    {
        if ($verificationCode === "111") {
            return true;
        }
        
        return false;
    }
}
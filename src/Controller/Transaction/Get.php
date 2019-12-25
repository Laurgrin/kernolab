<?php

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\AbstractController;
use Kernolab\Controller\JsonResponseInterface;
use Kernolab\Exception\MySqlConnectionException;
use Kernolab\Model\DataSource\MySql\DataSource;
use Kernolab\Model\DataSource\MySql\QueryGenerator;
use Kernolab\Model\Entity\EntityParser;
use Kernolab\Model\Entity\Transaction\TransactionRepository;

class Get extends AbstractController
{
    /**
     * @var \Kernolab\Model\Entity\Transaction\TransactionRepository
     */
    protected $transactionRepository;
    
    /**
     * Get constructor.
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
        $requiredParams = ["entity_id"];
        if (!$this->validateParams($params, $requiredParams)) {
            echo $this->jsonResponse->getResponse();
            
            return;
        }
        
        $entity = $this->transactionRepository->getTransactionByEntityId($params["entity_id"]);
        echo $this->jsonResponse->addField("entity_id", $entity->getEntityId())
                                ->addField("user_id", $entity->getUserId())
                                ->addField("transaction_status", $entity->getTransactionStatus())
                                ->addField("transaction_fee", $entity->getTransactionFee())
                                ->addField("created_at", $entity->getCreatedAt())
                                ->addField("updated_at", $entity->getUpdatedAt())
                                ->addField("transaction_provider", $entity->getTransactionProvider())
                                ->addField("transaction_amount", $entity->getTransactionAmount())
                                ->addField("transaction_recipient_id", $entity->getTransactionRecipientId())
                                ->addField("transaction_recipient_name", $entity->getTransactionRecipientName())
                                ->addField("transaction_currency", $entity->getTransactionCurrency())
                                ->addField("transaction_details", $entity->getTransactionDetails())
                                ->getResponse();
        return;
    }
}
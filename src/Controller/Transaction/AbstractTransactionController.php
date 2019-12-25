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

abstract class AbstractTransactionController extends AbstractController
{
    /**
     * @var \Kernolab\Model\Entity\Transaction\TransactionRepository
     */
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
}
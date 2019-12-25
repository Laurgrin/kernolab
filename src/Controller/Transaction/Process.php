<?php

namespace Kernolab\Controller\Transaction;

class Process extends AbstractTransactionController
{
    
    /**
     * Process a request and return a response
     *
     * @param array $params
     *
     * @return mixed
     */
    public function execute(array $params)
    {
        $limit = $params["limit"];
        $this->transactionRepository->processTransactions($limit);
    }
}
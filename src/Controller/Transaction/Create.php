<?php

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\AbstractController;

class Create extends AbstractController
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
        print_r($params);
    }
}
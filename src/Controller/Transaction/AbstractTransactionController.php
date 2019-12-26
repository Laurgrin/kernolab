<?php

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\AbstractController;
use Kernolab\Controller\JsonResponseInterface;
use Kernolab\Model\Entity\Transaction\TransactionRepositoryInterface;

/** @codeCoverageIgnore */
abstract class AbstractTransactionController extends AbstractController
{
    /**
     * @var \Kernolab\Model\Entity\Transaction\TransactionRepository
     */
    protected $transactionRepository;
    
    /**
     * AbstractTransactionController constructor.
     *
     * @param \Kernolab\Controller\JsonResponseInterface                        $jsonResponse
     * @param \Kernolab\Model\Entity\Transaction\TransactionRepositoryInterface $transactionRepository
     */
    public function __construct(
        JsonResponseInterface $jsonResponse,
        TransactionRepositoryInterface $transactionRepository
    ) {
        parent::__construct($jsonResponse);
        $this->transactionRepository = $transactionRepository;
    }
}
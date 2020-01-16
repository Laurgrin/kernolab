<?php declare(strict_types = 1);

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\AbstractController;
use Kernolab\Controller\JsonResponse;
use Kernolab\Service\Logger;
use Kernolab\Service\RequestValidator;
use Kernolab\Service\TransactionService;

/** @codeCoverageIgnore */
abstract class AbstractTransactionController extends AbstractController
{
    /**
     * @var TransactionService
     */
    protected $transactionService;
    
    /**
     * AbstractTransactionController constructor.
     *
     * @param JsonResponse             $jsonResponse
     * @param RequestValidator         $requestValidator
     * @param \Kernolab\Service\Logger $logger
     * @param TransactionService       $transactionService
     */
    public function __construct(
        JsonResponse $jsonResponse,
        RequestValidator $requestValidator,
        Logger $logger,
        TransactionService $transactionService
    ) {
        parent::__construct($jsonResponse, $requestValidator, $logger);
        $this->transactionService = $transactionService;
    }
}
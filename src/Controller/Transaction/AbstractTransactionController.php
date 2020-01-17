<?php declare(strict_types = 1);

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\AbstractController;
use Kernolab\Controller\JsonResponse;
use Kernolab\Service\Logger;
use Kernolab\Service\RequestValidator;
use Kernolab\Service\TransactionControllerExceptionHandler;
use Kernolab\Service\TransactionService;

/** @codeCoverageIgnore */
abstract class AbstractTransactionController extends AbstractController
{
    /**
     * @var TransactionService
     */
    protected $transactionService;
    
    /**
     * @var \Kernolab\Service\TransactionControllerExceptionHandler
     */
    protected $controllerExceptionHandler;
    
    /**
     * AbstractTransactionController constructor.
     *
     * @param JsonResponse                                            $jsonResponse
     * @param RequestValidator                                        $requestValidator
     * @param \Kernolab\Service\Logger                                $logger
     * @param TransactionService                                      $transactionService
     * @param \Kernolab\Service\TransactionControllerExceptionHandler $controllerExceptionHandler
     */
    public function __construct(
        JsonResponse $jsonResponse,
        RequestValidator $requestValidator,
        TransactionService $transactionService,
        TransactionControllerExceptionHandler $controllerExceptionHandler
    ) {
        parent::__construct($jsonResponse, $requestValidator);
        $this->transactionService = $transactionService;
        $this->controllerExceptionHandler = $controllerExceptionHandler;
    }
}
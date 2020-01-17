<?php declare(strict_types = 1);

namespace Kernolab\Controller\Transaction;

use Kernolab\Controller\AbstractController;
use Kernolab\Controller\JsonResponse;
use Kernolab\Service\RequestValidator;
use Kernolab\Service\ExceptionHandler;
use Kernolab\Service\TransactionService;

/** @codeCoverageIgnore */
abstract class AbstractTransactionController extends AbstractController
{
    /**
     * @var TransactionService
     */
    protected $transactionService;
    
    /**
     * @var \Kernolab\Service\ExceptionHandler
     */
    protected $controllerExceptionHandler;
    
    /**
     * AbstractTransactionController constructor.
     *
     * @param JsonResponse                       $jsonResponse
     * @param RequestValidator                   $requestValidator
     * @param TransactionService                 $transactionService
     * @param \Kernolab\Service\ExceptionHandler $exceptionHandler
     */
    public function __construct(
        JsonResponse $jsonResponse,
        RequestValidator $requestValidator,
        TransactionService $transactionService,
        ExceptionHandler $exceptionHandler
    ) {
        parent::__construct($jsonResponse, $requestValidator);
        $this->transactionService = $transactionService;
        $this->controllerExceptionHandler = $exceptionHandler;
    }
}
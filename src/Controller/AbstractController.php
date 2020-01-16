<?php declare(strict_types = 1);

namespace Kernolab\Controller;

use Kernolab\Service\Logger;
use Kernolab\Service\RequestValidator;

/** @codeCoverageIgnore */
abstract class AbstractController implements ControllerInterface
{
    /**
     * @var \Kernolab\Controller\JsonResponse
     */
    protected $jsonResponse;
    
    /**
     * @var \Kernolab\Service\RequestValidator
     */
    protected $requestValidator;
    
    /**
     * @var \Kernolab\Service\Logger
     */
    protected $logger;
    
    /**
     * AbstractController constructor.
     *
     * @param \Kernolab\Controller\JsonResponse  $jsonResponse
     * @param \Kernolab\Service\RequestValidator $requestValidator
     * @param \Kernolab\Service\Logger           $logger
     */
    public function __construct(JsonResponse $jsonResponse, RequestValidator $requestValidator, Logger $logger)
    {
        $this->jsonResponse     = $jsonResponse;
        $this->requestValidator = $requestValidator;
        $this->logger = $logger;
    }
}
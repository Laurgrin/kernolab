<?php declare(strict_types = 1);

namespace Kernolab\Routing;

use Kernolab\Controller\JsonResponse;
use Kernolab\Service\Container;
use Kernolab\Service\RequestSanitizer;
use Kernolab\Service\ResponseHandler;
use Kernolab\Service\Logger;

/**
 * Class AbstractRouter
 * @package Kernolab\Routing
 * @codeCoverageIgnore
 */
abstract class AbstractRouter implements RouterInterface
{
    public const CONTROLLER_NAMESPACE = "\\Kernolab\\Controller\\";
    
    /**
     * @var JsonResponse
     */
    protected $jsonResponse;
    
    /**
     * @var array
     */
    protected $routes;
    
    /**
     * @var Container
     */
    protected $container;
    
    /**
     * @var RequestSanitizer
     */
    protected $requestSanitizer;
    
    /**
     * @var ResponseHandler
     */
    protected $responseHandler;
    
    /**
     * @var \Kernolab\Service\Logger
     */
    protected $logger;
    
    /**
     * AbstractRouter constructor.
     *
     * @param JsonResponse             $jsonResponse
     * @param Container                $container
     * @param RequestSanitizer         $requestSanitizer
     * @param ResponseHandler          $responseHandler
     * @param \Kernolab\Service\Logger $logger
     */
    public function __construct(
        JsonResponse $jsonResponse,
        Container $container,
        RequestSanitizer $requestSanitizer,
        ResponseHandler $responseHandler,
        Logger $logger
    ) {
        $this->jsonResponse     = $jsonResponse;
        $this->container        = $container;
        $this->requestSanitizer = $requestSanitizer;
        $this->responseHandler  = $responseHandler;
        $this->logger           = $logger;
        $this->routes           = json_decode(
            file_get_contents(ROUTE_PATH),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
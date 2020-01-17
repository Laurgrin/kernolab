<?php declare(strict_types = 1);

namespace Kernolab\Routing;

use Kernolab\Controller\JsonResponse;
use Kernolab\Service\Container;
use Kernolab\Service\ExceptionHandler;
use Kernolab\Service\RequestSanitizer;
use Kernolab\Service\ResponseHandler;
use Kernolab\Service\RouteResolver;

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
     * @var \Kernolab\Service\ExceptionHandler
     */
    protected $exceptionHandler;
    
    /**
     * @var \Kernolab\Service\RouteResolver
     */
    protected $routeResolver;
    
    /**
     * AbstractRouter constructor.
     *
     * @param JsonResponse                       $jsonResponse
     * @param Container                          $container
     * @param RequestSanitizer                   $requestSanitizer
     * @param ResponseHandler                    $responseHandler
     * @param \Kernolab\Service\ExceptionHandler $exceptionHandler
     * @param \Kernolab\Service\RouteResolver    $routeResolver
     */
    public function __construct(
        JsonResponse $jsonResponse,
        Container $container,
        RequestSanitizer $requestSanitizer,
        ResponseHandler $responseHandler,
        ExceptionHandler $exceptionHandler,
        RouteResolver $routeResolver
    ) {
        $this->jsonResponse     = $jsonResponse;
        $this->container        = $container;
        $this->requestSanitizer = $requestSanitizer;
        $this->responseHandler  = $responseHandler;
        $this->exceptionHandler = $exceptionHandler;
        $this->routeResolver = $routeResolver;
    }
}
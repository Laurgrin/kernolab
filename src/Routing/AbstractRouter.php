<?php declare(strict_types = 1);

namespace Kernolab\Routing;

use Kernolab\Controller\JsonResponse;
use Kernolab\Service\DependencyInjectionContainer;
use Kernolab\Service\RequestSanitizer;
use Kernolab\Service\ResponseHandler;

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
     * @var DependencyInjectionContainer
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
     * AbstractRouter constructor.
     *
     * @param JsonResponse                 $jsonResponse
     * @param DependencyInjectionContainer $container
     * @param RequestSanitizer             $requestSanitizer
     * @param ResponseHandler              $responseHandler
     */
    public function __construct(
        JsonResponse $jsonResponse,
        DependencyInjectionContainer $container,
        RequestSanitizer $requestSanitizer,
        ResponseHandler $responseHandler
    ) {
        $this->jsonResponse     = $jsonResponse;
        $this->container        = $container;
        $this->requestSanitizer = $requestSanitizer;
        $this->responseHandler = $responseHandler;
        $this->routes           = json_decode(
            file_get_contents(ROUTE_PATH),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
<?php  declare(strict_types = 1);

namespace Kernolab\Routing;

use Kernolab\Controller\JsonResponse;

/**
 * Class AbstractRouter
 * @package Kernolab\Routing
 * @codeCoverageIgnore
 */
abstract class AbstractRouter implements RouterInterface
{
    /**
     * @var \Kernolab\Controller\JsonResponse
     */
    protected $jsonResponse;
    
    /**
     * AbstractRouter constructor.
     *
     * @param \Kernolab\Controller\JsonResponse $jsonResponse
     */
    public function __construct(JsonResponse $jsonResponse)
    {
        $this->jsonResponse = $jsonResponse;
    }
    
    /**
     * Route the request to an appropriate handler (controller).
     *
     * @param string $requestUri
     *
     * @param string $requestMethod
     *
     * @return void
     */
    abstract public function route(string $requestUri, string $requestMethod): void;
}
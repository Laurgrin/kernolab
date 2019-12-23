<?php

namespace Kernolab\Routing;

use Kernolab\Controller\JsonResponseInterface;

abstract class AbstractRouter implements RouterInterface
{
    /**
     * @var \Kernolab\Controller\JsonResponseInterface
     */
    protected $jsonResponse;
    
    /**
     * AbstractRouter constructor.
     *
     * @param \Kernolab\Controller\JsonResponseInterface $jsonResponse
     */
    public function __construct(JsonResponseInterface $jsonResponse)
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
    abstract public function route(string $requestUri, string $requestMethod);
}
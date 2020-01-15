<?php  declare(strict_types = 1);

namespace Kernolab\Routing;

/**
 * Interface RouterInterface
 * @package Kernolab\Routing
 * @codeCoverageIgnore
 */
interface RouterInterface
{
    /**
     * Route the request to an appropriate handler (controller).
     *
     * @param string $requestUri
     *
     * @param string $requestMethod
     *
     * @return void
     */
    public function route(string $requestUri, string $requestMethod);
}
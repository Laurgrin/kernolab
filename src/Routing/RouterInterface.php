<?php

namespace Kernolab\Routing;

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
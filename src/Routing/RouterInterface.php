<?php

namespace Routing;

interface RouterInterface
{
    /**
     * Route the request to an appropriate handler (controller).
     *
     * @return void
     */
    public function route();
}
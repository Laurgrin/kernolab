<?php

namespace Kernolab\Controller;

interface ControllerInterface
{
    /**
     * Process a request and return a response
     *
     * @param array $params
     *
     * @return mixed
     */
    public function execute(array $params);
}
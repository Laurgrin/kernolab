<?php

namespace Kernolab\Controller;

/**
 * Interface ControllerInterface
 * @package Kernolab\Controller
 * @codeCoverageIgnore
 */
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
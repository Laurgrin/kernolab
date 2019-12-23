<?php

namespace Kernolab\Controller;

abstract class AbstractController implements ControllerInterface
{
    /**
     * @var \Kernolab\Controller\JsonResponseInterface
     */
    protected $jsonResponse;
    
    public function __construct(JsonResponseInterface $jsonResponse)
    {
        $this->jsonResponse = $jsonResponse;
        header("Content-Type: application/json");
        header("Encoding: utf-8");
    }
}
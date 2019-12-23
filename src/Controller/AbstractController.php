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
    }
}
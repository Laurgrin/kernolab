<?php

namespace Kernolab\Controller;

/** @codeCoverageIgnore  */
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
    
    /**
     * Validates if all the required request params are there. Does not care about their values though.
     *
     * @param array $params
     *
     * @param array $requiredKeys
     *
     * @return bool
     */
    protected function validateParams(array $params, array $requiredKeys): bool
    {
        $requestKeys  = array_keys($params);
        if (array_intersect($requestKeys, $requiredKeys) === $requiredKeys) {
            return true;
        }
        
        $missingParams = array_diff($requiredKeys, $requestKeys);
        foreach ($missingParams as $missingParam) {
            $this->jsonResponse->addError(400, "Missing required argument {$missingParam}.");
        }
        
        return false;
    }
}
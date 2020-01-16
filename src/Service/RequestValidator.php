<?php declare(strict_types = 1);

namespace Kernolab\Service;

use Kernolab\Exception\RequestParameterException;

class RequestValidator
{
    /**
     * Validates if all the required request params are there. Does not care about their values though.
     *
     * @param array $params
     *
     * @param array $requiredKeys
     *
     * @return bool
     * @throws \Kernolab\Exception\RequestParameterException
     */
    public function validateRequest(array $params, array $requiredKeys): bool
    {
        $requestKeys = array_keys($params);
        if (array_intersect($requestKeys, $requiredKeys) === $requiredKeys) {
            return true;
        }
    
        throw new RequestParameterException(
            'The request has missing parameters',
            array_diff($requiredKeys, $requestKeys)
        );
    }
}
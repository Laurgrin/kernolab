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
     * @return void
     * @throws \Kernolab\Exception\RequestParameterException
     */
    public function validateRequest(array $params, array $requiredKeys): void
    {
        $requestKeys = array_keys($params);
        $diff        = array_diff($requestKeys, $requiredKeys);
        if (!empty($diff)) {
            throw new RequestParameterException(
                'The request has missing parameters',
                $diff
            );
        }
    }
}
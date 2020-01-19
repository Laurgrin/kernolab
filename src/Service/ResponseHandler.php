<?php declare(strict_types = 1);

namespace Kernolab\Service;

use Kernolab\Controller\JsonResponse;

class ResponseHandler
{
    /**
     * ResponseHandler constructor. Sets the response header.
     */
    public function __construct()
    {
        header('Content-Type: application/json');
        header('Encoding: utf-8');
    }
    
    /**
     * Echoes out the response. If we get a JSON exception somehow, we return a hard coded json response instead,
     * because the normal way we return any JSON response just threw an exception.
     *
     * @param \Kernolab\Controller\JsonResponse $response
     */
    public function handleResponse(JsonResponse $response): void
    {
        try {
            http_response_code($response->getStatusCode());
            echo $response->getResponse();
        } catch (\JsonException $e) {
            echo '{ "status": "error", "errors":
            [{"code": 500, "message": "An internal error has occurred while creating a response"}]}';
        }
    }
}
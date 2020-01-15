<?php declare(strict_types = 1);

namespace Kernolab\Controller;

class JsonResponse
{
    /**
     * @var array
     */
    protected $response = [];
    
    /**
     * Adds a data field to the response or modifies an existing one.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return \Kernolab\Controller\JsonResponse
     */
    public function addField(string $key, $value): JsonResponse
    {
        $this->response[$key] = $value;
        
        return $this;
    }
    
    /**
     * Adds an error to the response.
     *
     * @param int    $code
     * @param string $message
     *
     * @return \Kernolab\Controller\JsonResponse
     */
    public function addError(int $code, string $message): JsonResponse
    {
        
        $this->response['status']   = 'error';
        $this->response['errors'][] = [
            'code'    => $code,
            'message' => $message,
        ];
        
        return $this;
    }
    
    /**
     * Returns a JSON encoded response.
     *
     * @return string
     */
    public function getResponse(): string
    {
        $response       = json_encode(
            $this->response,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_LINE_TERMINATORS,
            512
        );
        $this->response = [];
        
        return $response;
    }
}
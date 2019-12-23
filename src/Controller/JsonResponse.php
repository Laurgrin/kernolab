<?php

namespace Kernolab\Controller;

class JsonResponse implements JsonResponseInterface
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
     * @return \Kernolab\Controller\JsonResponseInterface
     */
    public function addField(string $key, $value): JsonResponseInterface
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
     * @return \Kernolab\Controller\JsonResponseInterface
     */
    public function addError(int $code, string $message): JsonResponseInterface
    {
        
        $this->response["status"]   = "error";
        $this->response["errors"][] = [
            "code"    => $code,
            "message" => $message,
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
        $response       = json_encode($this->response, JSON_UNESCAPED_LINE_TERMINATORS );
        $this->response = [];
        
        return $response;
    }
}
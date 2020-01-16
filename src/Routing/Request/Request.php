<?php declare(strict_types = 1);

namespace Kernolab\Routing\Request;

class Request
{
    /**
     * @var string
     */
    protected $requestUri;
    
    /**
     * @var string
     */
    protected $requestMethod;
    
    /**
     * @var array
     */
    protected $requestParams;
    
    /**
     * @var string
     */
    protected $controller;
    
    /**
     * @param string $requestUri
     *
     * @return Request
     */
    public function setRequestUri(string $requestUri): Request
    {
        $this->requestUri = $requestUri;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getRequestUri(): string
    {
        return $this->requestUri;
    }
    
    /**
     * @param string $requestMethod
     *
     * @return Request
     */
    public function setRequestMethod(string $requestMethod): Request
    {
        $this->requestMethod = $requestMethod;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }
    
    /**
     * @param array $requestParams
     *
     * @return Request
     */
    public function setRequestParams(array $requestParams): Request
    {
        $this->requestParams = $requestParams;
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getRequestParams(): array
    {
        return $this->requestParams;
    }
    
    /**
     * @param string $controller
     *
     * @return Request
     */
    public function setController(string $controller): Request
    {
        $this->controller = $controller;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }
}
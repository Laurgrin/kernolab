<?php

namespace Kernolab\Routing;

use Kernolab\Controller\JsonResponseInterface;

class Router extends AbstractRouter
{
    const CONTROLLER_NAMESPACE = "\\Kernolab\\Controller\\";
    
    /**
     * @var array
     */
    protected $routes;
    
    /**
     * Router constructor.
     *
     * @param \Kernolab\Controller\JsonResponseInterface $jsonResponse
     */
    public function __construct(JsonResponseInterface $jsonResponse)
    {
        parent::__construct($jsonResponse);
        $this->routes = json_decode(file_get_contents(ROUTE_PATH), true);
    }
    
    /**
     * Route the request to an appropriate handler (controller).
     *
     * @param string $requestUri
     *
     * @param string $requestMethod
     *
     * @return void
     */
    public function route(string $requestUri, string $requestMethod)
    {
        $requestUri = explode("?", $requestUri)[0];
        
        if (array_key_exists($requestUri, $this->routes)) {
            foreach ($this->routes as $uri => $route) {
                if ($requestUri === $uri) {
                    if ($route["method"] === $requestMethod) {
                        $controllerFqn = self::CONTROLLER_NAMESPACE . $route["controller"];
                        /** @var \Kernolab\Controller\ControllerInterface $controller */
                        $controller = new $controllerFqn($this->jsonResponse);
                        $controller->execute($_REQUEST);
                    } else {
                        echo $this->jsonResponse->addError(
                            "405",
                            "Method $requestMethod not allowed for this endpoint."
                        )->getResponse();
                    }
                }
            }
        } else {
            echo $this->jsonResponse->addError("404", "Endpoint $requestUri not found")->getResponse();
        }
    }
}
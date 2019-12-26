<?php

namespace Kernolab\Routing;

use Kernolab\Controller\ControllerInterface;
use Kernolab\Controller\JsonResponse;
use Kernolab\Controller\JsonResponseInterface;
use Kernolab\Exception\MySqlConnectionException;
use Kernolab\Model\DataSource\MySql\DataSource;
use Kernolab\Model\DataSource\MySql\QueryGenerator;
use Kernolab\Model\Entity\EntityParser;
use Kernolab\Model\Entity\Transaction\TransactionProviderRule;
use Kernolab\Model\Entity\Transaction\TransactionRepository;

/** @codeCoverageIgnore  */
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
                        try {
                            $controller = $this->instantiateControllerClass($route["controller"]);
                        } catch (MySqlConnectionException $e) {
                            echo $this->jsonResponse->addError("500", "There has been an internal error.")
                                                    ->getResponse();
                            return;
                        }
                        
                        $controller->execute($this->sanitize($this->getRequestParams($requestMethod)));
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
    
    /**
     * Filters the given array of data (POST or GET) and returns the sanitized array.
     *
     * @param array $data
     *
     * @return array
     */
    protected function sanitize(array $data): array
    {
        $filteredData = [];
        
        foreach ($data as $key => $value) {
            $sanitizedValue     = filter_var(trim($value), FILTER_SANITIZE_STRING);
            $filteredData[$key] = $sanitizedValue;
        }
        
        return $filteredData;
    }
    
    /**
     * Gets request params based on the request method.
     *
     * @param string $method
     *
     * @return array
     */
    protected function getRequestParams(string $method): array
    {
        switch ($method) {
            case "GET":
                return $_GET;
            case "POST":
                return $_POST;
            default:
                return $_REQUEST;
        }
    }
    
    /**
     * Instantiates a controller based on controller name
     *
     * @param string $controllerName
     *
     * @return \Kernolab\Controller\ControllerInterface
     * @throws \Kernolab\Exception\MySqlConnectionException
     */
    protected function instantiateControllerClass(string $controllerName): ControllerInterface
    {
        $controllerEntity = explode("\\", $controllerName)[0];
        $controllerFqn    = self::CONTROLLER_NAMESPACE . $controllerName;
        $jsonResponse     = new JsonResponse();
        
        switch ($controllerEntity) {
            case "Transaction":
                $dataSource              = new DataSource(new QueryGenerator(), new EntityParser());
                $transactionProviderRule = new TransactionProviderRule();
                $repository              = new TransactionRepository($dataSource, $transactionProviderRule);
                
                return new $controllerFqn($jsonResponse, $repository);
            default:
                return new $controllerFqn($jsonResponse);
        }
    }
}
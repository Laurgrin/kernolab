<?php declare(strict_types = 1);

namespace Kernolab\Service;

use Kernolab\Exception\ConfigurationFileNotFoundException;
use Kernolab\Exception\UndefinedRouteException;
use Kernolab\Routing\Request\Request;

class RouteResolver
{
    protected const PATTERN = '/(?<=\/)\d+/';
    
    /**
     * @var \Kernolab\Service\Container
     */
    protected $container;
    
    /**
     * @var array
     */
    protected $routes = [];
    
    /**
     * @var \Kernolab\Service\RequestSanitizer
     */
    protected $sanitizer;
    
    public function __construct(Container $container, RequestSanitizer $sanitizer)
    {
        $this->container = $container;
        $this->sanitizer = $sanitizer;
    }
    
    /**
     * Gets routes from file.
     *
     * @return array
     * @throws \JsonException
     * @throws \Kernolab\Exception\ConfigurationFileNotFoundException
     */
    public function getRoutes(): array
    {
        if (empty($this->routes)) {
            if (!is_readable(ROUTE_PATH)) {
                throw new ConfigurationFileNotFoundException(sprintf('File %s not found', ROUTE_PATH));
            }
            
            $this->routes = json_decode(
                file_get_contents(ROUTE_PATH),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }
        
        return $this->routes;
    }
    
    /**
     * @param string $requestUri
     * @param string $requestMethod
     *
     * @return \Kernolab\Routing\Request\Request
     * @throws \JsonException
     * @throws \Kernolab\Exception\ContainerException
     * @throws \ReflectionException
     * @throws \Kernolab\Exception\ConfigurationFileNotFoundException
     * @throws \Kernolab\Exception\UndefinedRouteException
     */
    public function resolve(string $requestUri, string $requestMethod): Request
    {
        /** @var Request $request */
        $request = $this->container->get(Request::class);
        $request->setRequestUri($requestUri)->setRequestMethod($requestMethod);
        $routeName = $this->getRouteName($request);
        $routes    = $this->getRoutes();
        
        if (!array_key_exists($routeName, $routes)) {
            throw new UndefinedRouteException($routeName);
        }
        
        $request->setController($routes[$routeName]);
        $request->setRequestParams($this->getRequestParams($request));
        
        return $request;
    }
    
    /**
     * Returns a route name from route uri and method for matching against the routes file. Since we just want to match
     * a route here, we override the resource identifier for now.
     *
     * @param \Kernolab\Routing\Request\Request $request
     *
     * @return string
     */
    public function getRouteName(Request $request): string
    {
        $requestUri = preg_replace(self::PATTERN, '{id}', $request->getRequestUri());
        
        return strtoupper($request->getRequestMethod()) . $requestUri;
    }
    
    /**
     * Returns an array of params for the request.
     *
     * @param \Kernolab\Routing\Request\Request $request
     *
     * @return array
     */
    public function getRequestParams(Request $request): array
    {
        /* Gets the body data */
        if ($request->getRequestMethod() === 'PUT') {
            $contents = file_get_contents('php://input');
            parse_str($contents, $params);
        } else {
            $params = $this->sanitizer->sanitize($_REQUEST);
        }
        
        preg_match(self::PATTERN, $request->getRequestUri(), $matches, PREG_UNMATCHED_AS_NULL);
        if (!empty($matches)) {
            $params['entity_id'] = $matches[0];
        }
        
        return $params;
    }
}
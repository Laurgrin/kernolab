<?php declare(strict_types = 1);

namespace Kernolab\Routing;

use Kernolab\Exception\ContainerException;
use Kernolab\Routing\Request\Request;
use ReflectionException;

class Router extends AbstractRouter
{
    /**
     * Request the request to an appropriate handler (controller).
     *
     * @param string $requestUri
     *
     * @param string $requestMethod
     *
     * @return void
     */
    public function route(string $requestUri, string $requestMethod): void
    {
        try {
            /** @var Request $request */
            $request = $this->container->get(Request::class);
            $request->setRequestUri(explode('?', $requestUri)[0]);
    
            if (array_key_exists($request->getRequestUri(), $this->routes)) {
                $request->setRequestMethod($this->routes[$request->getRequestUri()]['method'])
                      ->setController($this->routes[$request->getRequestUri()]['controller']);
    
                if ($request->getRequestMethod() === $requestMethod) {
                    $params = '_' . strtoupper($request->getRequestMethod());
                    $request->setRequestParams($this->requestSanitizer->sanitize($$params));
                    /** @var \Kernolab\Controller\AbstractController $controller */
                    $controller = $this->container->get(self::CONTROLLER_NAMESPACE . $request->getController());
                    $this->jsonResponse = $controller->execute($request->getRequestParams());
                }
            } else {
                $this->jsonResponse->addError(404, sprintf('Endpoint %s not found', $requestUri));
            }
        } catch (ContainerException $e) {
            $this->jsonResponse->addError(500, 'An internal error has been encountered.');
        } catch (ReflectionException $e) {
            $this->jsonResponse->addError(500, 'An internal error has been encountered.');
        } finally {
            $this->responseHandler->handleResponse($this->jsonResponse);
        }
    }
}
<?php declare(strict_types = 1);

namespace Kernolab\Routing;

use Kernolab\Exception\ContainerException;
use Kernolab\Routing\Request\Request;
use Kernolab\Service\Logger;
use ReflectionException;

class Router extends AbstractRouter
{
    /**
     * Request the request to an appropriate handler (controller). Process the response.
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
                        ->setController($this->routes[$request->getRequestUri()]['controller'])
                        ->setRequestParams($this->requestSanitizer->sanitize($_REQUEST));
                
                if ($request->getRequestMethod() === $requestMethod) {
                    /** @var \Kernolab\Controller\AbstractController $controller */
                    $controller         = $this->container->get(self::CONTROLLER_NAMESPACE . $request->getController());
                    $this->jsonResponse = $controller->execute($request->getRequestParams());
                }
            } else {
                $this->jsonResponse->addError(404, sprintf('Endpoint %s not found', $requestUri));
            }
        } catch (ContainerException $e) {
            $this->jsonResponse->addError(500, 'An internal error has been encountered.');
            $this->logger->log(Logger::SEVERITY_ERROR, $e->getMessage());
        } catch (ReflectionException $e) {
            $this->jsonResponse->addError(500, 'An internal error has been encountered.');
            $this->logger->log(Logger::SEVERITY_ERROR, $e->getMessage());
        } finally {
            $this->responseHandler->handleResponse($this->jsonResponse);
        }
    }
}
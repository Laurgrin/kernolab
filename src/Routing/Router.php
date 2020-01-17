<?php declare(strict_types = 1);

namespace Kernolab\Routing;

use Kernolab\Exception\ConfigurationFileNotFoundException;
use Kernolab\Exception\ContainerException;
use Kernolab\Exception\UndefinedRouteException;
use ReflectionException;

class Router extends AbstractRouter
{
    /**
     * Request the request to an appropriate handler (controller). Process the response.
     *
     * @param string $requestUri
     * @param string $requestMethod
     *
     * @return void
     */
    public function route(string $requestUri, string $requestMethod): void
    {
        try {
            $request = $this->routeResolver->resolve($requestUri, $requestMethod);
            $controller         = $this->container->get(self::CONTROLLER_NAMESPACE . $request->getController());
            $this->jsonResponse = $controller->execute($request->getRequestParams());
        } catch (ContainerException $e) {
            $this->exceptionHandler->handleContainerException($e, $this->jsonResponse);
        } catch (ReflectionException $e) {
            $this->exceptionHandler->handleReflectionException($e, $this->jsonResponse);
        } catch (\JsonException $e) {
            $this->exceptionHandler->handleJsonException($e, $this->jsonResponse);
        } catch (ConfigurationFileNotFoundException $e) {
            $this->exceptionHandler->handleConfigurationFileNotFoundException($e, $this->jsonResponse);
        } catch (UndefinedRouteException $e) {
            $this->exceptionHandler->handleUndefinedRouteException($e, $this->jsonResponse);
        } finally {
            $this->responseHandler->handleResponse($this->jsonResponse);
        }
    }
}
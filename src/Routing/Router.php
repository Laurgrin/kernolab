<?php declare(strict_types = 1);

namespace Kernolab\Routing;

use Kernolab\Exception\ApiException;
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
     * @throws \Kernolab\Exception\ApiException
     */
    public function route(string $requestUri, string $requestMethod): void
    {
        try {
            $request = $this->routeResolver->resolve($requestUri, $requestMethod);
            $controller         = $this->container->get(self::CONTROLLER_NAMESPACE . $request->getController());
            $this->jsonResponse = $controller->execute($request->getRequestParams());
        } catch (ContainerException $e) {
            $this->exceptionHandler->handleContainerException($e);
        } catch (ReflectionException $e) {
            $this->exceptionHandler->handleReflectionException($e);
        } catch (\JsonException $e) {
            $this->exceptionHandler->handleJsonException($e);
        } catch (ConfigurationFileNotFoundException $e) {
            $this->exceptionHandler->handleConfigurationFileNotFoundException($e);
        } catch (UndefinedRouteException $e) {
            $this->exceptionHandler->handleUndefinedRouteException($e);
        } catch (ApiException $e) {
            $this->jsonResponse->addError($e->getCode(), $e->getMessage());
        } finally {
            $this->responseHandler->handleResponse($this->jsonResponse);
        }
    }
}
<?php declare(strict_types = 1);

namespace Kernolab\Service;

use Closure;
use Kernolab\Exception\ContainerException;
use ReflectionClass;
use ReflectionException;

class DependencyInjectionContainer
{
    /**
     * @var array
     */
    protected $instances = [];
    
    /**
     * Sets the implementation for an abstract.
     *
     * @param      $abstract
     * @param null $concrete
     */
    public function set($abstract, $concrete = NULL): void
    {
        if ($concrete === NULL) {
            $concrete = $abstract;
        }
        $this->instances[$abstract] = $concrete;
    }
    
    /**
     * Gets the implementation of an abstract
     *
     * @param       $abstract
     * @param array $parameters
     *
     * @return mixed|null|object
     * @throws ReflectionException
     * @throws \Kernolab\Exception\ContainerException
     */
    public function get($abstract, $parameters = [])
    {
        // if we don't have it, just register it
        if (!isset($this->instances[$abstract])) {
            $this->set($abstract);
        }
        
        return $this->resolve($this->instances[$abstract], $parameters);
    }
    
    /**
     * Resolve a single object instantiation.
     *
     * @param $concrete
     * @param $parameters
     *
     * @return mixed|object
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function resolve($concrete, $parameters)
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        $reflector = new ReflectionClass($concrete);
        
        if (!$reflector->isInstantiable()) {
            throw new ContainerException(sprintf('Class %s is not instantiable', $concrete));
        }
        
        $constructor = $reflector->getConstructor();
        if ($constructor === null) {
            return $reflector->newInstance();
        }
        
        $parameters   = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters);
        
        return $reflector->newInstanceArgs($dependencies);
    }
    
    /**
     * Resolves dependencies recursively.
     *
     * @param \ReflectionParameter[] $parameters
     *
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function getDependencies($parameters): array
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if ($dependency === NULL) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new ContainerException(sprintf('Can not resolve class dependency %s', $parameter->name));
                }
            } else {
                $dependencies[] = $this->get($dependency->name);
            }
        }
        
        return $dependencies;
    }
}
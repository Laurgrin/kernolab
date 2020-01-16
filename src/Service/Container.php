<?php declare(strict_types = 1);

namespace Kernolab\Service;

use Closure;
use Kernolab\Exception\ContainerException;
use ReflectionClass;
use ReflectionException;

class Container
{
    /**
     * @var array
     */
    protected $instances = [];
    
    /**
     * @var array
     */
    protected $implementations;
    
    public function __construct()
    {
        $this->implementations = json_decode(
            file_get_contents(DI_PATH),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
    
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
     * @throws \JsonException
     */
    public function get($abstract, $parameters = [])
    {
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
     * @throws \JsonException
     */
    public function resolve($concrete, $parameters)
    {
        
        
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        $reflector = new ReflectionClass($concrete);
        
        if (!$reflector->isInstantiable()) {
            if (array_key_exists($concrete, $this->implementations)) {
                $reflector = new ReflectionClass($this->implementations[$reflector->getName()]);
            } else {
                throw new ContainerException(sprintf('Class %s is not instantiable', $concrete));
            }
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
     * @throws \JsonException
     * @throws \Kernolab\Exception\ContainerException
     * @throws \ReflectionException
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
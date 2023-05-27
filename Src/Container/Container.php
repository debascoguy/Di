<?php

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */

namespace Emma\Di\Container;

use Emma\Di\Container\Interfaces\ContainerInterface;
use Emma\Di\Singleton\SingletonInterface\SingletonInterface;
use Emma\Di\Singleton\Singleton;
use Emma\Di\Property\Property;
use InvalidArgumentException;

class Container extends Property implements ContainerInterface, SingletonInterface
{
    use Singleton;

    /**
     * CREATE and REGISTER object in Container before return statement.
     * @param $concrete
     * @param null $parameters
     * @return mixed|object
     * @throws InvalidArgumentException
     */
    public function get($concrete, $parameters = null)
    {
        $className = is_object($concrete) ? get_class($concrete) : $concrete;
        if (parent::has($className)) {
            return parent::get($className);
        }
        $object = $this->create($concrete, $parameters);
        $this->register(get_class($object), $object);
        return $object;
    }

     /**
      * CREATE object without registering it in Container.

     * @param $concrete
     * @param null $parameters
     * @return mixed|object
     * @throws InvalidArgumentException
     */
    public function create($concrete, $parameters = null)
    {
        $className = is_object($concrete) ? get_class($concrete) : $concrete;
        if (parent::has($className)) {
            return parent::get($className);
        }
        elseif (is_object($concrete) && !($concrete instanceof \ReflectionClass)) {
            $object = clone $concrete;
        }
        elseif ($concrete instanceof SingletonInterface || is_callable([$className, "getInstance"])) {
            $object = call_user_func([$className, "getInstance"]);
        }
        else {
            $object = $this->resolve($className, $parameters);
        }
        return $object;
    }
    
    /**
     * @param $concrete
     * @param $parameters
     * @return object
     * @throws InvalidArgumentException
     */
    private function resolve($concrete, $parameters)
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this, $parameters);
        }
        if ($concrete instanceof \ReflectionClass) {
            $reflector = $concrete;
        }
        else{
            $reflector = new \ReflectionClass($concrete);
        }
        if (!$reflector->isInstantiable()) {
            throw new InvalidArgumentException("Class {$concrete} is not instantiable");
        }
        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return $reflector->newInstance();
        }
        $parameters   = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters);
        return $reflector->newInstanceArgs($dependencies);
    }
    
    /**
     * @param \ReflectionParameter[] $parameters
     * @return array
     */
    public function getDependencies(array $parameters)
    {
        $params = [];
        foreach($parameters as $parameter) {
            $type = $parameter->getType();
            if ($type && !$type->isBuiltin()) {
                $params[$parameter->getName()] = $this->get($type->getName());
            }
            elseif ($parameter->isDefaultValueAvailable()) {
                $params[$parameter->getName()] = $parameter->getDefaultValue();
            }
            else{
                $params[$parameter->getName()] =  null;
            }
        }
        return $params;
    }

    /**
     * @return array
     */
    public function getContainer(): array
    {
        return $this->getParameters();
    }

    /**
     * @param array $container
     * @return self
     */
    public function setContainer(array $container): self
    {
        return $this->setParameters($container);
    }    
}
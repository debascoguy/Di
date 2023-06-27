<?php

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
namespace Emma\Di\Container;

use Emma\Common\Singleton\Interfaces\SingletonInterface;
use InvalidArgumentException;

trait ObjectCreator
{
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
        if (is_object($concrete) && !($concrete instanceof \ReflectionClass)) {
            $object = clone $concrete;
        }
        elseif ($concrete instanceof SingletonInterface || is_callable([$className, "getInstance"])) {
            $object = call_user_func([$className, "getInstance"], $parameters);
        }
        else {
            $object = $this->resolve($className, $parameters);
        }
        return $object;
    }

    /**
     * @param $concrete
     * @param array|null $parameterValues
     * @return mixed|object|null
     * @throws \ReflectionException
     */
    private function resolve($concrete, array $parameterValues = null)
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this, $parameterValues);
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
        $constructorParameters   = $constructor->getParameters();
        if (empty($constructorParameters)) {
            return $reflector->newInstance();
        }
        $dependencies = $this->getDependencies($constructorParameters, $parameterValues);
        if (!empty($parameterValues)) {
            $dependencies = array_merge($dependencies, $parameterValues);
        }
        return $reflector->newInstanceArgs($dependencies);
    }
    
    /**
     * @param \ReflectionParameter[] $parameters
     * @return array
     */
    public function getDependencies(array $parameters, array $parameterValues = null)
    {
        $params = [];
        foreach($parameters as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();
            if (!($type instanceof \ReflectionUnionType)
                && !empty($parameterValues)
                && isset($parameterValues[$name])
                && is_object($parameterValues[$name])) {
                $params[$name] = $parameterValues[$name];
            }
            elseif ($type && !($type instanceof \ReflectionUnionType) && !$type->isBuiltin()) {
                $params[$name] = $this->get($type->getName());
            }
            elseif ($parameter->isDefaultValueAvailable()) {
                $params[$name] = $parameter->getDefaultValue();
            }
            else{
                $params[$name] =  null;
            }
        }
        return $params;
    }

    /**
     * @param $concrete
     * @param null $parameters
     * @return mixed|object
     * @throws InvalidArgumentException
     */
    public function get($concrete, $parameters = null)
    {
        return $this->create($concrete, $parameters);
    }
}
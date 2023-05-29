<?php
namespace Emma\Di;

use Closure;
use Emma\Di\Autowire\Autowire;
use Emma\Di\Container\ContainerManager;
use Emma\Common\Singleton\Singleton;
use InvalidArgumentException;

class DiFactory
{
    use ContainerManager, Singleton;
    
    /**
     * @param \Closure|string|array|object $callable
     * @return @callable
     */
    public function injectCallable($callable, &$callableParams = [])
    {
        if (is_array($callable)) {
            return $this->injectArrayCallable($callable, $callableParams);
        }
        
        if (class_exists($callable)) {
            $callable = $this->inject($callable);
            return $callable;
        }

        if ($callable instanceof \Closure || is_string($callable)) {
            $callableParams = $this->injectClosureParameters($callable);
            return $callable;
        }

        return $callable;
    }
    
    /**
     * @param $class
     * @return object
     * @throws InvalidArgumentException
     */
    public function inject($class)
    {
        $instance = new Autowire($class);
        return $instance->execute();
    }

    /**
     * @param array $callable
     * @referencedParam $callableParams
     * @return $callable
     */
    public function injectArrayCallable(array $callable, &$callableParams = [])
    {
        if (class_exists($callable[0])) {
            $callable[0] = $this->inject($callable[0]);
            $callableParams = $this->injectMethodParameters($callable[0], $callable[1]);
            return $callable;
        }
        return $callable;
    }

    /**
     * @param \Closure|string $callable
     * @referencedParam $callableParams
     * @return array
     */
    public function injectClosureParameters($callable)
    {
        if ($callable instanceof \Closure || is_string($callable)) {
            $ref = new \ReflectionFunction($callable);
            $parameters   = $ref->getParameters();
            return $this->getContainer()->getDependencies($parameters);
        }
        return [];
    }

    /**
     * @param object|string $objectOrMethod
     * @param string $method
     * @return array
     */
    public function injectMethodParameters($objectOrMethod, string $method)
    {
        $reflectionMethod = new \ReflectionMethod($objectOrMethod, $method);
        $parameters = $reflectionMethod->getParameters();
        return $this->getContainer()->getDependencies($parameters);
    }
}

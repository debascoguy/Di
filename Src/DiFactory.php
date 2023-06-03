<?php
namespace Emma\Di;

use Closure;
use Emma\Common\CallBackHandler\CallBackHandler;
use Emma\Di\Container\ContainerManager;
use Emma\Common\Singleton\Singleton;
use Emma\Di\Autowire\AutowireFunction;
use Emma\Di\Autowire\AutowireMethod;
use Emma\Di\Autowire\AutowireProperty;
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
            return $this->findInjectablePropertiesAndMethodParameters($callable, $callableParams);
        }
        
        if (class_exists($callable)) {
            $callable = $this->injectObjectProperties($callable);
            return $callable;
        }

        if ($callable instanceof \Closure || is_string($callable)) {
            $callableParams = $this->findInjectableClosureParameters($callable);
            return $callable;
        }

        return $callable;
    }

    /**
     * @param $callable
     */
    public function invokeCallable($callable, &$callableParams = [])
    {
        if (is_array($callable) && is_callable($callable)) {
            $callable = $this->findInjectablePropertiesAndMethodParameters($callable, $callableParams);
            return CallBackHandler::get($callable, $callableParams);
        }
        
        if (class_exists($callable)) {
            $callable = $this->injectObjectProperties($callable);
            return $callable;
        }

        if ($callable instanceof \Closure || is_string($callable)) {
            $callableParams = array_merge($this->findInjectableClosureParameters($callable), $callableParams);
            return CallBackHandler::get($callable, $callableParams);
        }
        return $callable;
    }
    
    /**
     * @param array $callable
     * @referencedParam $callableParams
     * @return $callable
     */
    public function findInjectablePropertiesAndMethodParameters(array $callable, &$callableParams = [])
    {
        if (class_exists($callable[0])) {
            $callable[0] = $this->injectObjectProperties($callable[0]);
            $callableParams = $this->findInjectableMethodParameters($callable[0], $callable[1]);
            return $callable;
        }
        return $callable;
    }

    /**
     * @param $class
     * @return object
     * @throws InvalidArgumentException
     */
    public function injectObjectProperties($class)
    {
        return (new AutowireProperty())->autowire($class);
    }

    /**
     * @param \Closure|string $callable
     * @referencedParam $callableParams
     * @return array
     */
    public function findInjectableClosureParameters($callable)
    {
        return (new AutowireFunction())->autowire($callable);
    }

    /**
     * @param object|string $objectOrMethod
     * @param string $method
     * @return array
     */
    public function findInjectableMethodParameters($objectOrMethod, string $method)
    {
        return (new AutowireMethod())->autowire($objectOrMethod, $method);
    }
}

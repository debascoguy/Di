<?php
namespace Emma\Di;

use Closure;
use Emma\Common\CallBackHandler\CallBackHandler;
use Emma\Di\Container\ContainerManager;
use Emma\Common\Singleton\Singleton;
use Emma\Di\Autowire\AutowiredFunction;
use Emma\Di\Autowire\AutowiredMethod;
use Emma\Di\Autowire\AutowiredProperty;
use InvalidArgumentException;

class DiFactory
{
    use ContainerManager, Singleton;

    /**
     * @param $callable
     * @param array $callableParams
     * @return array|Closure|mixed|object|string
     * @throws \ReflectionException
     */
    public function injectCallable($callable, array &$callableParams = []): mixed
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
     * @param array $callableParams
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function invokeCallable($callable, array &$callableParams = []): mixed
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
     * @param array $callableParams
     * @return array
     * @throws \ReflectionException
     */
    public function findInjectablePropertiesAndMethodParameters(array $callable, array &$callableParams = []): array
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
    public function injectObjectProperties($class): object
    {
        return (new AutowiredProperty())->autowire($class);
    }

    /**
     * @param string|\Closure $callable
     * @referencedParam $callableParams
     * @return array
     */
    public function findInjectableClosureParameters(string|Closure $callable): array
    {
        return (new AutowiredFunction())->autowire($callable);
    }

    /**
     * @param object|string $objectOrMethod
     * @param string $method
     * @return array
     * @throws \ReflectionException
     */
    public function findInjectableMethodParameters(object|string $objectOrMethod, string $method): array
    {
        return (new AutowiredMethod())->autowire($objectOrMethod, $method);
    }
}

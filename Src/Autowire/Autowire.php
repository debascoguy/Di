<?php

namespace Emma\Di\Autowire;

use Emma\Di\Autowire\Interfaces\AutowireInterface;
use Emma\Di\Container\ContainerManager;

class Autowire implements AutowireInterface
{
    use ContainerManager;

    /**
     * @var AutowireInterface
     */
    protected AutowireInterface $autowiredPropertyFactory;

    /**
     * @var AutowireInterface
     */
    protected AutowireInterface $autowiredMethodFactory;

    /**
     * @var bool
     */
    protected bool $isReady = false;

    /**
     * @var AutowireInterface
     */
    protected AutowireInterface $autowiredFunctionFactory;

    public function __construct()
    {
        /** @var AutowireFactory $factory */
        $factory = $this->getContainer()->create(AutowireFactory::class);
        $this->autowiredPropertyFactory = $factory;
        $this->autowiredMethodFactory = clone $factory;
        $this->autowiredFunctionFactory = clone $factory;
    }

    /**
     * @return $this
     */
    public function make(): static
    {
        if ($this->isReady) {
            return $this;
        }
        $this->autowiredPropertyFactory->make(AutowireProperty::class);
        $this->autowiredMethodFactory->make(AutowireMethod::class);
        $this->autowiredFunctionFactory->make(AutowireFunction::class);
        $this->isReady = true;
        return $this;
    }

    /**
     * @param object|string|callable|array $objectOrClassOrCallable
     * @param array|string $parameterOrMethod
     * @return array|callable|object|null
     */
    public function autowire(
        object|string|callable|array $objectOrClassOrCallable,
        array|string &$parameterOrMethod
    ): mixed {
        $this->make();
        if (is_array($objectOrClassOrCallable) && is_callable($objectOrClassOrCallable, true)) {
            return $this->findInjectablePropertiesAndMethodParameters($objectOrClassOrCallable, $parameterOrMethod);
        }

        if (class_exists($objectOrClassOrCallable)) {
            $objectOrClassOrCallable = $this->autowiredPropertyFactory->autowire($objectOrClassOrCallable);
        }

        if (is_string($parameterOrMethod) && method_exists($objectOrClassOrCallable, $parameterOrMethod)) {
            return $this->autowiredMethodFactory->autowire($objectOrClassOrCallable, $parameterOrMethod);
        }

        if ($objectOrClassOrCallable instanceof \Closure || is_string($objectOrClassOrCallable)) {
            return $this->autowiredFunctionFactory->autowire($objectOrClassOrCallable);
        }

        return $objectOrClassOrCallable;
    }

    /**
     * @param array $callable
     * @param array $callableParams
     * @return array
     */
    private function findInjectablePropertiesAndMethodParameters(array $callable, array &$callableParams = []): array
    {
        if (class_exists($callable[0])) {
            $callable[0] = $this->autowiredPropertyFactory->autowire($callable[0]);
            $diParams = $this->autowiredMethodFactory->autowire($callable[0], $callable[1]);
            foreach ($diParams as $key => $param) {
                foreach($callableParams as $key2 => $value) {
                    if (strtolower($key2) == strtolower($key)) {
                        $diParams[$key] = $value;
                    }
                }
            }
            $callableParams = $diParams;
            return $callable;
        }
        return $callable;
    }

}
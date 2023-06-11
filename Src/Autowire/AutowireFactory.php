<?php

namespace Emma\Di\Autowire;

use Emma\Di\Autowire\Interfaces\AutowireInterface;
use Emma\Di\Container\ContainerManager;
use Emma\Common\Factory\AbstractFactory;

class AutowireFactory extends AbstractFactory implements AutowireInterface
{
    use ContainerManager;

    protected AutowireInterface $autowire;

    /**
     * @param array|string|null $param
     * @return AutowireInterface
     */
    public function make(array|string $param = null): AutowireInterface
    {
        if (is_null($param)) {
            $param = Autowire::class;
        }
        $this->autowire = $this->getContainer()->get($param);
        return $this;
    }

    /**
     * @param object|string|callable|array $objectOrClassOrCallable
     * @param array|string|null $parameterOrMethod
     * @return mixed
     */
    public function autowire(object|string|callable|array $objectOrClassOrCallable, array|string &$parameterOrMethod = null): mixed
    {
        return $this->autowire->autowire($objectOrClassOrCallable, $parameterOrMethod);
    }
}
<?php

namespace Emma\Di\Autowire;

use Emma\Di\Autowire\Helper\AutowireMethodHelper;
use Emma\Di\Autowire\Interfaces\AutowireInterface;
use Emma\Di\Container\ContainerManager;

class AutowireFunction implements AutowireInterface
{
    use ContainerManager, AutowireMethodHelper;

    /**
     * @param object|string $callable
     * @return array $parameter [requested autowired Dependencies]
     * @throws \InvalidArgumentException
     */
    public function autowire($callable)
    {
        if ( !($callable instanceof \Closure || is_string($callable)) ) {
            return [];
        }

        $reflectionMethod = new \ReflectionFunction($callable);
        $parameters = $reflectionMethod->getParameters();
        return $this->helper(
            $parameters, 
            $reflectionMethod->getDocComment() ?? ""
        );
    }

}
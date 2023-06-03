<?php

namespace Emma\Di\Autowire;

use Emma\Di\Autowire\Helper\AutowireMethodHelper;
use Emma\Di\Autowire\Interfaces\AutowireInterface;
use Emma\Di\Container\ContainerManager;

class AutowireMethod implements AutowireInterface
{
    use ContainerManager, AutowireMethodHelper;
    
    /**
     * @param object|string $objectOrMethod
     * @param string $method
     * @return array $parameter [requested autowired Dependencies]
     * @throws \InvalidArgumentException
     */
    public function autowire($objectOrMethod, string $method)
    {
        $reflectionMethod = new \ReflectionMethod($objectOrMethod, $method);
        $parameters = $reflectionMethod->getParameters();
        return $this->helper(
            $parameters,
            $reflectionMethod->getDocComment() ?? ""
        );
    }

}
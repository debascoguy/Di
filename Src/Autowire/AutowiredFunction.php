<?php

namespace Emma\Di\Autowire;

use Emma\Di\Attribute\Inject;
use Emma\Di\Autowire\Helper\AutowiredMethodHelper;
use Emma\Di\Autowire\Interfaces\AutowireInterface;
use Emma\Di\Container\ContainerManager;
use Emma\Di\Resolver\InjectAttributeParametersResolver;

class AutowiredFunction implements AutowireInterface
{
    use ContainerManager, AutowiredMethodHelper;

    /**
     * @param \Closure|string $callable
     * @return array $parameter [requested autowired Dependencies]
     * @throws \ReflectionException
     */
    public function autowire(\Closure|string $callable)
    {
        $reflectionFunction = new \ReflectionFunction($callable);
        $parameters = $reflectionFunction->getParameters();
        $attributes = $reflectionFunction->getAttributes(Inject::class);
        $attributesParameters = InjectAttributeParametersResolver::resolve($attributes);
        return $this->helper(
            $parameters,
            $reflectionFunction->getDocComment() ?? "",
            $attributesParameters
        );
    }

}
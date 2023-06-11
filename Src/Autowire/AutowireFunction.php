<?php

namespace Emma\Di\Autowire;

use Emma\Di\Attribute\Inject;
use Emma\Di\Autowire\Helper\AutowiredMethodHelper;
use Emma\Di\Autowire\Interfaces\AutowireInterface;
use Emma\Di\Container\ContainerManager;
use Emma\Di\Resolver\InjectAttributeParametersResolver;

class AutowireFunction implements AutowireInterface
{
    use ContainerManager, AutowiredMethodHelper;

    /**
     * @param object|string|callable|array $callable
     * @param array|string|null $_
     * @return array
     * @throws \ReflectionException
     */
    public function autowire(
        object|string|callable|array $callable,
        array|string &$_ = null
    ): array {

        $reflectionFunction = $callable instanceof \ReflectionFunction ? $callable : new \ReflectionFunction($callable);

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
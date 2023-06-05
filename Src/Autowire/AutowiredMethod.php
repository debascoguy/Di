<?php

namespace Emma\Di\Autowire;

use Emma\Di\Attribute\InjectProperty;
use Emma\Di\Attribute\Inject;
use Emma\Di\Autowire\Helper\AutowiredMethodHelper;
use Emma\Di\Autowire\Interfaces\AutowireInterface;
use Emma\Di\Container\ContainerManager;
use Emma\Di\Resolver\InjectAttributeParametersResolver;

class AutowiredMethod implements AutowireInterface
{
    use ContainerManager, AutowiredMethodHelper;

    /**
     * @param object|string $objectOrMethod
     * @param string $method
     * @return array $parameter [requested autowired Dependencies]
     * @throws \ReflectionException
     */
    public function autowire(object|string $objectOrMethod, string $method): array
    {
        $reflectionMethod = new \ReflectionMethod($objectOrMethod, $method);
        $parameters = $reflectionMethod->getParameters();
        $attributes = $reflectionMethod->getAttributes(Inject::class);
        $attributesParameters = InjectAttributeParametersResolver::resolve($attributes);

        return $this->helper(
            $parameters,
            $reflectionMethod->getDocComment() ?? "",
            $attributesParameters
        );
    }

}
<?php

namespace Emma\Di\Autowire;

use Emma\Di\Attribute\InjectProperty;
use Emma\Di\Attribute\Inject;
use Emma\Di\Autowire\Helper\AutowiredMethodHelper;
use Emma\Di\Autowire\Interfaces\AutowireInterface;
use Emma\Di\Container\ContainerManager;
use Emma\Di\Resolver\InjectAttributeParametersResolver;

class AutowireMethod implements AutowireInterface
{
    use ContainerManager, AutowiredMethodHelper;

    /**
     * @param object|string $objectOrClassOrMethod
     * @param string $method
     * @return array $parameter [requested autowired Dependencies]
     * @throws \ReflectionException
     */
    public function autowire(
        object|string|callable|array $objectOrClassOrCallable,
        array|string &$parameterOrMethod
    ): array {

        $reflectionMethod = $objectOrClassOrCallable instanceof \ReflectionMethod ?
            $objectOrClassOrCallable :
            new \ReflectionMethod($objectOrClassOrCallable, $parameterOrMethod);

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
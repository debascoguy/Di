<?php

namespace Emma\Di\Resolver;

use Emma\Di\Attribute\Inject;

class InjectAttributeParametersResolver
{

    /**
     * @param array| \ReflectionAttribute[] $attributes
     * @return array
     */
    public static function resolve(array $attributes): array
    {
        if (empty($attributes))
            return [];

        $attributesParameters = [];
        array_walk($attributes, function (\ReflectionAttribute $attr) use (&$attributesParameters) {
            /** @var Inject $injectInstance */
            $injectInstance = $attr->newInstance();
            $attributesParameters = array_merge($attributesParameters, $injectInstance->getParameters());
        });
        return $attributesParameters;
    }

}
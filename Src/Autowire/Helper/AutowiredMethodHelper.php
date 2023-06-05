<?php

namespace Emma\Di\Autowire\Helper;

use Emma\Di\Annotation\Annotation;
use Emma\Di\Annotation\AnnotationProperty;
use Emma\Di\Resolver\AnnotationResolver;

trait AutowiredMethodHelper
{
    use ParameterHelper;

    /**
     * @param array|\ReflectionParameter[] $parameters
     * @param string $docComment
     * @param array $attributesParameters
     * @return array [requested autowired Dependencies]
     */
    protected function helper(array $parameters, string $docComment, array $attributesParameters): array
    {
        if (empty($parameters)) {
            return [];
        }

        $injectables = $injectableBaseNames = [];
        if (!empty($docComment) && str_contains($docComment, Annotation::NAME)) {
            $injectables = AnnotationResolver::resolveAll($docComment, $injectableBaseNames);
        }
        $params = [];
        foreach($parameters as $parameter) {
            $paramName = $parameter->getName();
            if (array_key_exists($paramName, $attributesParameters)) {
                $params[$paramName] = $this->getParameterValue(
                    [[AnnotationProperty::NAME => $attributesParameters[$paramName]]],
                    $parameter
                );
            }
            else if (!empty($injectableBaseNames) && in_array(basename($parameter->getType()->getName()), $injectableBaseNames)) {
                $params[$paramName] = $this->getParameterValue($injectables, $parameter);
            }
            else {
                $params[$paramName] = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
            }
        }
        return $params;
    }

}

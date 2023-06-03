<?php

namespace Emma\Di\Autowire\Helper;

use Emma\Di\Annotation\AnnotationProperty;
use Emma\Di\Resolver\AnnotationResolver;

trait AutowireMethodHelper
{
    /**
     * @param array $parameters
     * @param string $docComment
     * @return array $parameter [requested autowired Dependencies]
     */
    protected function helper(array $parameters, string $docComment)
    {
        if (empty($parameters)) {
            return [];
        }
        
        $injectableBaseNames = [];
        $injectables = AnnotationResolver::resolveAll($docComment, $injectableBaseNames);

        $params = [];
        foreach($parameters as $parameter) {
            if (!in_array(basename($parameter->getType()->getName()), $injectableBaseNames)) {
                $params[$parameter->getName()] = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
                continue;
            }
            $params[$parameter->getName()] = $this->getParameterValue($injectables, $parameter);
        }
        return $params;
    }

    /**
     * @param array $injectables
     * @param \ReflectionParameter $parameter
     */
    private function getParameterValue(array $injectables, \ReflectionParameter $parameter)
    {
        $type = $parameter->getType();
        $typeName = $type->getName();
        $typeBaseName =  basename($typeName);
        $configs = $this->getContainer()->get("CONFIG_VARS");

        foreach($injectables as $injectDetails) {
            if (isset($injectDetails[AnnotationProperty::CONFIG]) 
                && ($injectDetails[AnnotationProperty::CONFIG] == $typeBaseName || $injectDetails[AnnotationProperty::CONFIG] == $typeName)) {
                $value = $configs[$injectDetails[AnnotationProperty::CONFIG]] ?? null;
                return $value;
            }

            if (isset($injectDetails[AnnotationProperty::NAME]) 
                && ($injectDetails[AnnotationProperty::NAME] == $typeBaseName || $injectDetails[AnnotationProperty::NAME] == $typeName)) {
                if ($type && !$type->isBuiltin()) {
                    return $this->getContainer()->get($typeName);
                }
                elseif ($parameter->isDefaultValueAvailable()) {
                    return $parameter->getDefaultValue();
                }
                else{
                    return  null;
                }
            }
        }
        return null;
    }
}

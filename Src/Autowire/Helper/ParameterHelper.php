<?php

namespace Emma\Di\Autowire\Helper;

use Emma\Di\Annotation\AnnotationProperty;

trait ParameterHelper
{
    /**
     * @param array $injectables
     * @param \ReflectionParameter $parameter
     * @return mixed|object|null
     */
    private function getParameterValue(array $injectables, \ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();
        $typeName = $type->getName();
        $typeBaseName =  basename($typeName);
        $configs = $this->getContainer()->get("CONFIG_VARS");

        foreach($injectables as $injectDetails) {
            if (isset($injectDetails[AnnotationProperty::CONFIG])
                && ($injectDetails[AnnotationProperty::CONFIG] == $typeBaseName || $injectDetails[AnnotationProperty::CONFIG] == $typeName)) {
                return $configs[$injectDetails[AnnotationProperty::CONFIG]] ?? null;
            }

            if (isset($injectDetails[AnnotationProperty::NAME])
                && ($injectDetails[AnnotationProperty::NAME] == $typeBaseName || $injectDetails[AnnotationProperty::NAME] == $typeName)) {
                if (!$type->isBuiltin()) {
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
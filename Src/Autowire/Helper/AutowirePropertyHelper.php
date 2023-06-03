<?php

namespace Emma\Di\Autowire\Helper;

use Emma\Di\Annotation\Annotation;
use Emma\Di\Annotation\AnnotationProperty;
use Emma\Di\Resolver\AnnotationResolver;
use Emma\Di\Utils\StringManagement;

trait AutowirePropertyHelper
{
    /**
     * @param object
     * @return object
     */
    protected function helper($object)
    {
        $configs = $this->getContainer()->get("CONFIG_VARS");
        $reflector = new \ReflectionObject($object);
        $props = $reflector->getProperties();
        foreach ($props as $prop) {
            $docComment = $prop->getDocComment();
            if (!StringManagement::contains($docComment, Annotation::NAME)) {
                continue;
            }

            $prop->setAccessible(true);
            $value = $prop->getValue($object);
            if (!empty($value)) {
                continue; //Continue if injectable already set.
            }

            $injectDetails = AnnotationResolver::resolve($reflector, $prop);
            if (isset($injectDetails[AnnotationProperty::CONFIG])) {
                $value = $configs[$injectDetails[AnnotationProperty::CONFIG]] ?? null;
                $prop->setValue($object, $value);
                continue;
            }

            if (!empty($injectDetails[AnnotationProperty::NAME])) {
                $valueObject = $this->getContainer()->get($injectDetails[AnnotationProperty::NAME]);
                $valueObject = (new self())->autowire($valueObject);
                $prop->setValue($object, $valueObject);
            }
        }
        return $object;
    }
}
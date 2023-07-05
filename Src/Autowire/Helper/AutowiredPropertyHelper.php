<?php

namespace Emma\Di\Autowire\Helper;

use Emma\Di\Annotation\Annotation;
use Emma\Di\Annotation\AnnotationProperty;
use Emma\Di\Attribute\Inject;
use Emma\Di\Resolver\AnnotationResolver;
use Emma\Di\Utils\StringManagement;

trait AutowiredPropertyHelper
{
    /**
     * @param $object
     * @return object
     */
    protected function helper($object): object
    {
        $configs = $this->getContainer()->has("CONFIG_VARS") ? $this->getContainer()->get("CONFIG_VARS") : [];
        $reflector = new \ReflectionObject($object);
        $props = $reflector->getProperties();
        foreach ($props as $prop) {
            $attributes = $prop->getAttributes(Inject::class);
            $docComment = $prop->getDocComment();
            if (!StringManagement::contains($docComment, Annotation::NAME) && empty($attributes)) {
                continue;
            }

            $prop->setAccessible(true);
            $value = $prop->getValue($object);
            if (!empty($value)) {
                continue; //Continue if injectable already set.
            }

            if (!empty($attributes)) {
                $injectInstance = $attributes[0]->newInstance();
                $valueObject = $this->getContainer()->get($injectInstance->getName());
                $valueObject = (new self())->autowire($valueObject);
                $prop->setValue($object, $valueObject);
                continue;
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
<?php

namespace Emma\Di\Resolver;

use Emma\Di\Annotation\Annotation;
use Emma\Di\Annotation\AnnotationProperty;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class AnnotationResolver
{
    /**
     * @param $reflector
     * @param $prop
     * @return array
     */
    public static function resolve($reflector, $prop): array
    {
        $docComment = $prop->getDocComment();
        $injectableInfo = CommentResolver::resolve(
            $docComment, 
            str_replace("@", "", Annotation::NAME)
        );
        return is_array($injectableInfo) ? 
                $injectableInfo :
                [AnnotationProperty::NAME => NamespaceResolver::resolve($reflector, $prop, $injectableInfo)];
    }

    /**
     * @param string $docComment
     * @param array $injectableBaseNames
     * @return array|null
     */
    public static function resolveAll(string $docComment, array &$injectableBaseNames): ?array
    {
        return CommentResolver::resolveAll(
            $docComment, 
            str_replace("@", "", Annotation::NAME),
            $injectableBaseNames
        );
    }

}
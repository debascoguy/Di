<?php

namespace Emma\Di\Resolver;

use Emma\Di\Annotation\Annotation;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class AnnotationResolver
{

    public static function resolve($reflector, $prop)
    {
        $docComment = $prop->getDocComment();
        $injectableInfo = CommentResolver::resolve(
            $docComment, 
            str_replace("@", "", Annotation::NAME)
        );
        return is_array($injectableInfo) ? 
                $injectableInfo :
                ['name' => NamespaceResolver::resolve($reflector, $prop, $injectableInfo)];
    }

}
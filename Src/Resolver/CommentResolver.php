<?php

namespace Emma\Di\Resolver;

use Emma\Di\Annotation\AnnotationProperty;

class CommentResolver 
{
    /**
     * @param $docComment
     * @param string $_at
     * @return array|string|null
     */
    public static function resolve($docComment, string $_at = "\w+"): array|string|null
    {
        $pattern = '/\@'.$_at.'\s+[\\\.\w+]+/i';
        if (preg_match($pattern, $docComment, $matches)){
            return !empty($matches[0]) ? trim(preg_replace('/\@\w+\s+/i', '', $matches[0])) : null;
        }

        $pattern = '/\@'.$_at.'\s*\(\w+=["\']\w+["\']\)+/i';
        if (preg_match($pattern, $docComment, $matches)){
            $details = !empty($matches[0]) ? trim(preg_replace('/\@\w+\s*/i', '', $matches[0])) : null;
            $details = str_replace(['(', ')', '"', '\''], '', $details);
            $temp = explode("=", $details);
            return [$temp[0] => $temp[1]];
        }
        
        return null;
    }

    /**
     * @param string $docComment
     * @param string $_at
     * @param array $injectableBaseNames  (param reference)
     * @return array|null
     */
    public static function resolveAll(string $docComment, string $_at = "\w+", array &$injectableBaseNames = []): ?array
    {
        $pattern = '/\@'.$_at.'\s+[\\\.\w+]+/i';
        if (preg_match_all($pattern, $docComment, $matches)) {
            return !empty($matches) ? array_map(function($m) use (&$injectableBaseNames) {
                $value = trim(preg_replace('/\@\w+\s+/i', '', $m));
                $injectableBaseNames[] = basename($value);
                return [AnnotationProperty::NAME => $value];
            }, $matches[0]) : null;
        }

        $pattern = '/\@'.$_at.'\s*\(\w+=["\']\w+["\']\)+/i';
        if (preg_match_all($pattern, $docComment, $matches)) {
            return !empty($matches) ? array_map(function($m) use (&$injectableBaseNames) {
                $details = !empty($m) ? trim(preg_replace('/\@\w+\s*/i', '', $m)) : null;
                $details = str_replace(['(', ')', '"', '\''], '', (string)$details);
                $temp = explode("=", $details);
                $injectableBaseNames[] = basename($temp[1]);
                return [$temp[0] => $temp[1]];
            }, $matches[0]) : null;
        }

        return null;
    }

}
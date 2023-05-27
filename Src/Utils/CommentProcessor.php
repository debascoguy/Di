<?php

namespace Emma\Di;

class CommentProcessor {

    /**
     * @param $docComment
     * @return mixed|string
     */
    public static function processAnnotationFromComment($docComment, $_at = null)
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

        $pattern = '/\@\w+\s+[\\\.\w+]+/i';
        if (empty($_at)){
            preg_match_all($pattern, $docComment, $matches);
            return $matches;
        }
        
        return null;
    }
    
    /**
     * @param \ReflectionMethod $reflect
     * @return array|null
     */
    public static function processPHPDoc(\ReflectionMethod $reflect)
    {
        $phpDoc = array('params' => array(), 'return' => null);
        $docComment = $reflect->getDocComment();
        if (trim($docComment) == '') {
            return null;
        }
        $docComment = preg_replace('#[ \t]*(?:\/\*\*|\*\/|\*)?[ ]{0,1}(.*)?#', '$1', $docComment);
        $docComment = ltrim($docComment, "\r\n");
        $parsedDocComment = $docComment;
        $lineNumber = $firstBlandLineEncountered = 0;
        while (($newlinePos = strpos($parsedDocComment, "\n")) !== false) {
            $lineNumber++;
            $line = substr($parsedDocComment, 0, $newlinePos);

            $matches = array();
            if ((strpos($line, '@') === 0) && (preg_match('#^(@\w+.*?)(\n)(?:@|\r?\n|$)#s', $parsedDocComment, $matches))) {
                $tagDocblockLine = $matches[1];
                $matches2 = array();

                if (!preg_match('#^@(\w+)(\s|$)#', $tagDocblockLine, $matches2)) {
                    break;
                }
                $matches3 = array();
                if (!preg_match('#^@(\w+)\s+([\w|\\\]+)(?:\s+(\$\S+))?(?:\s+(.*))?#s', $tagDocblockLine, $matches3)) {
                    break;
                }
                if ($matches3[1] != 'param') {
                    if (strtolower($matches3[1]) == 'return') {
                        $phpDoc['return'] = array('type' => $matches3[2]);
                    }
                } else {
                    $phpDoc['params'][] = array('name' => $matches3[3], 'type' => $matches3[2]);
                }

                $parsedDocComment = str_replace($matches[1] . $matches[2], '', $parsedDocComment);
            }
        }
        return $phpDoc;
    }
}
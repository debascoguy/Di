<?php

namespace Emma\Di\Utils;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 * Date: 4/9/2016
 * Time: 8:52 PM
 */
class StringManagement
{
    /**
     * @param $string
     * @param string $replace_with
     * @return string
     */
    public static function strip_space($string, $replace_with = "")
    {
        return trim(preg_replace('/\s+/', $replace_with, $string));
    }

    /**
     * @param $haystack
     * @param $needle
     * @param bool $case_sensitive
     * @return bool
     */
    public static function startsWith($haystack, $needle, $case_sensitive = false)
    {
        $length = strlen($needle);
        if ($case_sensitive) {
            $value = substr($haystack, 0, $length);
            return (trim($needle) === "") || (strcasecmp($value, $needle) == 0);
        }
        return (trim($needle) === "") || (substr($haystack, 0, $length) === $needle);
    }

    /**
     * @param $haystack
     * @param $needle
     * @param bool $case_sensitive
     * @return bool
     */
    public static function endsWith($haystack, $needle, $case_sensitive = false)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        if ($case_sensitive) {
            $value = substr($haystack, -$length);
            return (trim($needle) === "") || (strcasecmp($value, $needle) == 0);
        }
        return (trim($needle) === "") || (substr($haystack, -$length) === $needle);
    }


    /**
     * @param $needle
     * @param $haystack
     * @return bool
     */
    public static function in_arrayi($needle, $haystack)
    {
        if (count($haystack) <= 0){
            return false;
        }
        return in_array(strtolower($needle), array_map('strtolower', $haystack));
    }


    /**
     * @param $_haystack
     * @param $_needle
     * @param bool $case_sensitive
     * @return bool
     */
    public static function contains($_haystack, $_needle, $case_sensitive = false)
    {
        if (is_array($_haystack)) {
            if (is_array($_needle)) {
                foreach ($_haystack as $elem) {
                    if ($elem === $_needle) {
                        return true;
                    }
                }
                return false;
            }
            return ($case_sensitive) ? in_array($_needle, $_haystack) : self::in_arrayi($_needle, $_haystack);
        } else {
            return ($case_sensitive) ? (strpos($_haystack, $_needle) !== false) : (stripos($_haystack, $_needle) !== false);
        }
    }

    /**
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    public static function str_ireplace($search, $replace, $subject) 
    {
        $search = preg_quote($search, "/");
        return preg_replace("/".$search."/i", $replace, $subject);
    }

    /**
     * @param string $string
     * @param string $separator
     * @return string
     */
    public static function camelCaseToUnderscore($string, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1' . $separator . '$2', $string));
    }

    /**
     * @param string $string
     * @param bool $ucFirst
     * @param string $separator
     * @return mixed
     */
    public static function underscoreToCamelCase($string, $ucFirst = false, $separator = '_')
    {
        $str = str_replace(' ', '', ucwords(str_replace($separator, ' ', $string)));
        if (!$ucFirst) {
            $str[0] = strtolower($str[0]);
        }
        return $str;
    }

    /**
     * @param $value
     * @param null $default
     * @return null
     */
    public static function getOrDefault($value, $default = null)
    {
        return (empty($value)) ? $default : $value;
    }

    /**
     * @param string $stringValue
     * @return bool
     */
    public static function toBoolean(string $stringValue): bool
    {
        return filter_var($stringValue, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param string $stringValue
     * @return int
     */
    public static function toInteger(string $stringValue): int
    {
        return filter_var($stringValue, FILTER_VALIDATE_INT);
    }

}
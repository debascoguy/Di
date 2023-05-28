<?php

namespace Emma\Di\Resolver;

use Emma\Di\Utils\StringManagement;

class NamespaceResolver
{
    /**
     * @param \ReflectionClass $reflector
     * @param \ReflectionProperty $prop
     * @param $className
     * @return mixed|null|string
     * @throws \InvalidArgumentException
     */
    public static function resolve(\ReflectionClass $reflector, \ReflectionProperty $prop, $className)
    {
        if (empty($className)){
            return ;
        }
        
        $InjectableClassName = $className;
        if (class_exists($InjectableClassName)) {
            return $InjectableClassName;
        }

        //Try using the namespace of the prop
        $InjectableClassName = $prop->getDeclaringClass()->getNamespaceName() . DIRECTORY_SEPARATOR . $className;
        if (class_exists($InjectableClassName)) {
            return $InjectableClassName;
        }

        //Try using the namespace of the reflector
        $InjectableClassName = $reflector->getNamespaceName() . DIRECTORY_SEPARATOR . $className;
        if (class_exists($InjectableClassName)) {
            return $InjectableClassName;
        }

        //Try using the namespace of the imported classes through the "use" keyword from the prop class file
        $InjectableClassName = self::getClass($prop->getDeclaringClass()->getFileName(), $className);
        if (class_exists($InjectableClassName)) {
            return $InjectableClassName;
        }
        throw new \InvalidArgumentException("Invalid Injectable Object: class name: $InjectableClassName not found! ");
    }

    /**
     * @param $file
     * @return array
     */
    public static function getAll($file){
        if ($handle = fopen($file, "r")) {
            $useClasses = [];
            while (($lineString = fgets($handle, 4096)) !== false) {
                $lineString = StringManagement::strip_space($lineString, " ");
                if (StringManagement::startsWith($lineString, "use")){
                    $useClass = StringManagement::strip_space(str_replace(["use ", ";"], "", $lineString));
                    if (version_compare(PHP_MAJOR_VERSION, 7, ">=")) {
                        if (strpos($useClass, "{") !== false ) {
                            $pos = strpos($useClass, "{");
                            $namespace = substr($useClass, 0, $pos);
                            $classes = explode(",", substr($useClass, $pos, strpos($useClass, "}")));
                            foreach($classes as $className) {
                                $useClasses[] = $namespace.$className;
                            }
                        }
                    }
                    else{
                        $useClasses[] = $useClass;
                    }
                }
                if (StringManagement::startsWith($lineString, "class")){
                    break;
                }
            }
            fclose($handle);
            return $useClasses;
        }
        return [];
    }
    
    /**
     * @param $file
     * @param $className
     * @return null|string
     */
    public static function getClass($file, $className){
        if ($handle = fopen($file, "r")) {
            $useClass = null;
            while (($lineString = fgets($handle, 4096)) !== false) {
                $lineString = StringManagement::strip_space($lineString, " ");
                if (StringManagement::startsWith($lineString, "use ") && StringManagement::contains($lineString, $className)) {
                    $useClass = StringManagement::strip_space(str_replace(["use ", ";"], "", $lineString));
                    if (version_compare(PHP_MAJOR_VERSION, 7, ">=")) {
                        if (strpos($useClass, "{") !== false ) {
                            $pos = strpos($useClass, "{");
                            $namespace = substr($useClass, 0, $pos);
                            $useClass = $namespace.$className;
                        }
                    }
                    break;
                }
                if (StringManagement::startsWith($lineString, "class")){
                    break;
                }
            }
            fclose($handle);
            return $useClass;
        }
        return null;
    }
}
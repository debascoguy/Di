<?php

namespace Emma\Di;
use Emma\Stdlib\StringManagement;

class DeclaredUseClass
{
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
                if (StringManagement::startsWith($lineString, "use") && StringManagement::contains($lineString, $className)) {
                    $lineString = str_replace(["use ", ";"], "", $lineString);
                    $useClass = StringManagement::strip_space($lineString);
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
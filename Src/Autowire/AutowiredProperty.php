<?php

namespace Emma\Di\Autowire;

use Emma\Di\Autowire\Helper\AutowiredPropertyHelper;
use Emma\Di\Autowire\Interfaces\AutowireInterface;
use Emma\Di\Container\ContainerManager;

class AutowiredProperty implements AutowireInterface
{
    use ContainerManager, AutowiredPropertyHelper;

    /**
     * @param $classOrObject
     * @return object|null
     */
    public function autowire($classOrObject): ?object
    {
        if (is_null($classOrObject)) {
            return null;
        }

        $object = is_object($classOrObject) ? $classOrObject : $this->getContainer()->create($classOrObject);
        $className = get_class($object);
        $classNameInjected = $className."::autowired";

        if ($this->getContainer()->has($classNameInjected)) {
            return $this->getContainer()->get($classNameInjected);
        }

        $object = $this->helper($object);
        
        $this->getContainer()->register($className, $object);
        $this->getContainer()->register($classNameInjected, $object);
        return $object;
    }
    
}
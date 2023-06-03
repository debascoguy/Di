<?php

namespace Emma\Di\Autowire;

use Emma\Di\Autowire\Helper\AutowirePropertyHelper;
use Emma\Di\Autowire\Interfaces\AutowireInterface;
use Emma\Di\Container\ContainerManager;

class AutowireProperty implements AutowireInterface
{
    use ContainerManager, AutowirePropertyHelper;

    /**
     * @param $classOrObject
     * @return object
     * @throws \InvalidArgumentException
     */
    public function autowire($classOrObject)
    {
        if (is_null($classOrObject)) {
            return $classOrObject;
        }

        $object = is_object($classOrObject) ? $classOrObject : $this->getContainer()->get($classOrObject);
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
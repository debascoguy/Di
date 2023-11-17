<?php

namespace Emma\Di\Autowire;

use Emma\Di\Autowire\Helper\AutowiredPropertyHelper;
use Emma\Di\Autowire\Interfaces\AutowireInterface;
use Emma\Di\Container\ContainerManager;

class AutowireProperty implements AutowireInterface
{
    use ContainerManager, AutowiredPropertyHelper;

    /**
     * @param object|string|callable|array $objectOrClassOrClass
     * @param array|string|null $_
     * @return object
     */
    public function autowire(
        object|string|callable|array $objectOrClassOrClass,
        array|string &$_ = null
    ): object {

        if (!is_object($objectOrClassOrClass) && !class_exists($objectOrClassOrClass)) {
            return $objectOrClassOrClass;
        }

        if (is_object($objectOrClassOrClass)) {
            $object = $objectOrClassOrClass;
            $isConstructed = true;
        } else {
            $object = $this->getContainer()->create($objectOrClassOrClass, null, true);
            $isConstructed = false;
        }

        $className = get_class($object);
        $classNameInjected = $className."::autowired";

        if ($this->getContainer()->has($classNameInjected)) {
            return $this->getContainer()->get($classNameInjected);
        }

        $object = $this->helper($object);
        if (!$isConstructed && method_exists($object, "__construct")) {
            $object->__construct();
        }
        
        $this->getContainer()->register($className, $object);
        $this->getContainer()->register($classNameInjected, $object);

        return $object;
    }
    
}
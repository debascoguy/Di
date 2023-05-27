<?php

namespace Emma\Di\Autowire;

use Emma\Di\Autowire\Interfaces\AutowireInterface;
use Emma\Di\CommentProcessor;
use Emma\Di\Container\ContainerManager;
use Emma\Di\DeclaredUseClass;
use Emma\Di\Utils\StringManagement;
use InvalidArgumentException;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 * Date: 8/19/2017
 * Time: 10:10 PM
 */
class Autowire implements AutowireInterface
{    
    use ContainerManager;

    /**
     * @var object
     */
    protected $object;

    /**
     * @var array
     */
    protected array $configs = [];

    /**
     * @var string
     */
    protected string $injectorAnnotation = "@Inject";

    /**
     * @param $class
     * @constructor
     */
    public function __construct($class)
    {
        $this->init($class);
    }

    /**
     * @param $class
     * @constructor
     */
    public function init($class)
    {
        if (!is_null($class)) {
            $this->setObject(is_object($class) ? $class : $this->getContainer()->get($class));
        }

        if ($this->getContainer()->has("CONFIG_VARS")) {
            $this->configs = $this->getContainer()->get("CONFIG_VARS");
            return $this;
        }
        return $this;
    }
    
    /**
     * @return object
     * @throws \InvalidArgumentException
     */
    public function execute()
    {
        $object = $this->getObject();
        $className = get_class($object);
        $classNameInjected = $className."::autowired";

        if ($this->getContainer()->has($classNameInjected)) {
            return $this->getContainer()->get($classNameInjected);
        }
        
        $reflector = new \ReflectionObject($object);
        $props = $reflector->getProperties();
        foreach ($props as $prop) {
            $docComment = $prop->getDocComment();
            if (StringManagement::contains($docComment, $this->injectorAnnotation)) {
                $prop->setAccessible(true);
                $value = $prop->getValue($this->getObject());
                if (empty($value)) { //Check if injectable already set.
                    $InjectableClassNameOrVariable = CommentProcessor::processAnnotationFromComment($docComment, "Inject");
                    if (is_array($InjectableClassNameOrVariable)) {
                        $object = $this->processVariables($object, $prop, $InjectableClassNameOrVariable);
                    }
                    else {
                        $object = $this->process($reflector, $prop, $InjectableClassNameOrVariable);
                    }
                }
            }
        }
        
        $this->getContainer()->register($className, $object);
        $this->getContainer()->register($classNameInjected, $object);
        return $object;
    }

    /**
     * @param $setter
     * @param array $injectDetails
     * @return object
     * @throws \InvalidArgumentException
     */
    protected function processVariables($object, $prop, $injectDetails)
    {
        if (isset($injectDetails['config'])) {
            $value = $this->configs[$injectDetails['config']] ?? null;
            $this->getContainer()->register($injectDetails['config'], $value);
        }
        else if (isset($injectDetails['name'])) {
            $value = $this->getContainer()->has($injectDetails['name']) ?
                    $this->getContainer()->get($injectDetails['name']) :
                    $this->configs[$injectDetails['name']] ?? null;
            $this->getContainer()->register($injectDetails['name'], $value);
        }
        else {
            throw new \InvalidArgumentException("Invalid Injectable With Details: ".key($injectDetails)." => ".implode(", ", $injectDetails));
        }
        $prop->setValue($object, $value);
        return $object;
    }

    /**
     * @param object $masterObject
     * @param \ReflectionClass $reflector
     * @param \ReflectionProperty $prop
     * @param $InjectableClassName
     * @return object
     */
    protected function process(\ReflectionClass $reflector, \ReflectionProperty $prop, $InjectableClassName)
    {
        $InjectableClass = $this->namespaceLookupForClassName($reflector, $prop, $InjectableClassName);
        $object = $this->getContainer()->get($InjectableClass);
        $prop->setValue($object);

        //Recursively handles all other @Inject Inside the requested Injectable class
        return (new self($object))->execute();
    }

    /**
     * @param \ReflectionClass $reflector
     * @param \ReflectionProperty $prop
     * @param $className
     * @return mixed|null|string
     * @throws \InvalidArgumentException
     */
    protected function namespaceLookupForClassName(\ReflectionClass $reflector, \ReflectionProperty $prop, $className)
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
        $InjectableClassName = DeclaredUseClass::getClass($prop->getDeclaringClass()->getFileName(), $className);
        if (class_exists($InjectableClassName)) {
            return $InjectableClassName;
        }
        throw new \InvalidArgumentException("Invalid Injectable Object: class name: $InjectableClassName not found! ");
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param object $object
     * @return self
     */
    public function setObject($object)
    {
        $this->object = $object;
        return $this;
    }

}
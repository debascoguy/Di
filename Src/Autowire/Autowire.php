<?php

namespace Emma\Di\Autowire;

use Emma\Di\Annotation\Annotation;
use Emma\Di\Autowire\Interfaces\AutowireInterface;
use Emma\Di\Container\ContainerManager;
use Emma\Di\Resolver\AnnotationResolver;
use Emma\Di\Utils\StringManagement;
use InvalidArgumentException;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class Autowire implements AutowireInterface
{    
    use ContainerManager;

    /**
     * @var object
     */
    protected $object;

    /**
     * @var string
     */
    protected string $injectorAnnotation = Annotation::NAME;

    /**
     * @var array
     */
    protected array $configs = [];

    /**
     * @param $class
     * @constructor
     */
    public function __construct($class)
    {
        if (!is_null($class)) {
            $this->setObject(is_object($class) ? $class : $this->getContainer()->get($class));
        }
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

        $this->setConfigs($this->getContainer()->get("CONFIG_VARS"));
        
        $reflector = new \ReflectionObject($object);
        $props = $reflector->getProperties();
        foreach ($props as $prop) {
            $docComment = $prop->getDocComment();
            if (StringManagement::contains($docComment, $this->injectorAnnotation)) {
                $prop->setAccessible(true);
                $value = $prop->getValue($object);
                if (!empty($value)) {
                    continue; //Continue if injectable already set.
                }

                $injectDetails = AnnotationResolver::resolve($reflector, $prop);
                if (isset($injectDetails['config'])) {
                    $value = $this->configs[$injectDetails['config']] ?? null;
                    $prop->setValue($object, $value);
                    continue;
                }

                if (!empty($injectDetails['name'])) {
                    $valueObject = $this->getContainer()->get($injectDetails['name']);
                    $valueObject = (new self($valueObject))->execute();
                    $prop->setValue($object, $valueObject);
                }
            }
        }
        
        $this->getContainer()->register($className, $object);
        $this->getContainer()->register($classNameInjected, $object);
        return $object;
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
    
    /**
     * @return  array
     */ 
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * @param  array  $configs
     * @return  self
     */ 
    public function setConfigs(array $configs)
    {
        $this->configs = $configs;
        return $this;
    }
}
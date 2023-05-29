<?php

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
namespace Emma\Di\Container;

use Emma\Di\Container\Interfaces\ContainerInterface;
use Emma\Common\Singleton\SingletonInterface\SingletonInterface;
use Emma\Common\Singleton\Singleton;
use Emma\Common\Property\Property;
use InvalidArgumentException;

class Container extends Property implements ContainerInterface, SingletonInterface
{
    use Singleton, ObjectCreator;

    /**
     * CREATE and REGISTER object in Container before return statement.
     * @param $concrete
     * @param null $parameters
     * @return mixed|object
     * @throws InvalidArgumentException
     */
    public function get($concrete, $parameters = null)
    {
        $className = is_object($concrete) ? get_class($concrete) : $concrete;
        if (parent::has($className)) {
            return parent::get($className);
        }
        $object = $this->create($concrete, $parameters);
        $this->register(get_class($object), $object);
        return $object;
    }

    /**
     * @return array
     */
    public function getContainer(): array
    {
        return $this->getParameters();
    }

    /**
     * @param array $container
     * @return self
     */
    public function setContainer(array $container): self
    {
        return $this->setParameters($container);
    }    
}
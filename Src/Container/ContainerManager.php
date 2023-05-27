<?php

/**
 * Created By: Ademola Aina
 * Email: debascoguy@gmail.com
 */
namespace Emma\Di\Container;

trait ContainerManager
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * ContainerManager constructor.
     */
    public function __construct()
    {
        if (!isset($this->container)) {
            $this->container = Container::getInstance();
        }
    }


    /**
     * @return Container
     */
    public function getContainer()
    {
        if (!isset($this->container)) {
            $this->container = Container::getInstance();
        }
        return $this->container;
    }

    /**
     * @param Container $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }

}
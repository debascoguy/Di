<?php

namespace Emma\Di\Container\Interfaces;

use Emma\Di\Property\Interfaces\PropertyInterface;

interface ContainerInterface extends PropertyInterface
{
    /**
     * @param $concrete
     * @param null $parameters
     * @return mixed|object
     * @throws BaseException
     */
    public function create($concrete, $parameters = null);

    /**
     * @return array
     */
    public function getContainer(): array;

    /**
     * @param array $container
     * @return self
     */
    public function setContainer(array $container): self;
}

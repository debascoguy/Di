<?php

namespace Emma\Di\Property\Interfaces;

interface PropertyInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param $id
     * @param $value
     * @return self
     */
    public function register($id, $value);

    /**
     * @param $name
     * @return mixed
     */
    public function get($name);

    /**
     * @param string $name
     * @return bool
     */
    public function has($name);
}

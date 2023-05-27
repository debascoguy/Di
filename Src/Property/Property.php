<?php

namespace Emma\Di\Property;

use Emma\Di\Property\Interfaces\PropertyInterface;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 * Date: 8/21/2017
 * Time: 11:43 AM
 */
class Property implements PropertyInterface
{

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }
        return null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __unset($name)
    {
        unset($this->parameters[$name]);
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @param $name
     * @param $concreteOrValue
     */
    public function register($name, $concreteOrValue)
    {
        $this->parameters[$name] = $concreteOrValue;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasValue($name)
    {
        return isset($this->parameters[$name]) && !empty($this->parameters[$name]);
    }

    /**
     * @param string $name
     * @param null $default
     * @return null|mixed
     */
    public function get($name, $default = null)
    {
        return isset($this->parameters[$name]) && !is_null($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    /**
     * @param $name
     * @return bool
     */
    public function remove($name)
    {
        if ($this->has($name)){
            unset($this->parameters[$name]);
            return true;
        }
        return false;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->parameters = [];
        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->parameters);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->parameters);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return  empty($this->parameters);
    }
}
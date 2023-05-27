<?php

namespace Emma\Di\Autowire\Interfaces;

interface AutowireInterface
{
    /**
     * @param object $object
     * @return self
     */
    public function setObject($object);
    
     /**
     * @return object
     * @throws \InvalidArgumentException
     */
    public function execute();


}
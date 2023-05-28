<?php

namespace Emma\Di\Autowire\Interfaces;

interface AutowireInterface
{    
    /**
     * @return object
     * @throws \InvalidArgumentException
     */
    public function execute();
    
}
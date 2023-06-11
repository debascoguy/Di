<?php

namespace Emma\Di\Autowire\Interfaces;

interface AutowireInterface
{

    public function autowire(object|string|callable|array $objectOrClassOrCallable, array|string &$parameterOrMethod): mixed;

}
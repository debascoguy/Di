<?php

namespace Emma\Di\Singleton\SingletonInterface;

interface SingletonInterface
{
    /**
     * @return object
     */
    public static function getInstance();

}
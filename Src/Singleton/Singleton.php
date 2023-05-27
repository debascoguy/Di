<?php
/**
 * Created by PhpStorm.
 * User: Adebowale Oguntokun
 * Date: 12/29/2019
 * Time: 5:53 PM
 */

namespace Emma\Di\Singleton;


trait Singleton
{
    private static $instance = null;

    /**
     * @return $this
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}
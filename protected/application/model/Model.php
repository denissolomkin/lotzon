<?php

abstract class Model
{
    /**
     * Model class instance
     * 
     * @var Model
     */
    protected static $_instance = null;

    protected function __construct() {}

    public static function instance() 
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new static::myClassName()();
        }

        return self::$_instance;
    }

    protected static function myClassName() 
    {
        throw new ModelException("myClassName must be overrided by Model class childs", 500);
    }
}

Application::import(PATH_APPLICATION . 'ApplicationException.php');

class ModelException extends ApplicationException
{

}



<?php

class Config 
{

    private static $_instance = null;

    private $_configs = array();

    private function __construct() {}

    public static function instance() 
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Config();
        }

        return self::$_instance;
    }


    public function __set($key, $value) 
    {
        $this->_configs[$key] = $value;
    }

    public function __get($key) 
    {
        if (isset($this->_configs[$key])) {
            return $this->_configs[$key];
        }

        return null;
    }
}
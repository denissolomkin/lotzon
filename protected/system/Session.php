<?php

class Session 
{
    private static $_instances = array();

    private function __construct() {}

    public static function Connect($name = 'default')
    {
        if (empty(self::$_instances[$name])) {
            self::$_instances[$name] = new Session();
            self::$_instances[$name]->setInstanceName($name);
            self::$_instances[$name]->start();

        }

        return self::$_instances[$name];
    }

    private $_instanceName = '';

    private function setInstanceName($name) 
    {
        $this->_instanceName = $name;

        return $this;
    }

    private function getInstanceName()
    {
        return $this->_instanceName;
    }

    public function getId() 
    {
        return session_id();
    }

    public function start()
    {
        session_start();

        return $this;
    }

    public function set($key, $value) 
    {
        $_SESSION[$key] = $value;

        return $this;
    }

    public function get($key, $default = null) 
    {
        if (!empty($_SESSION[$key])) {
            return $_SESSION[$key];
        } 

        return $default;
    }

    public function delete($key)
    {
        unset($_SESSION[$key]);

        return $this;
    }

    public function close()
    {
        session_destroy($this->getId());

        unset(self::$_instances[$this->getInstanceName()]);

        return true;
    }
}
<?php

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcacheSessionHandler;
use Symfony\Component\HttpFoundation\Request;

class Session2
{
    private static $_instances = array();

    private function __construct() {}

    public static function Connect($name = 'default')
    {
        if (empty(self::$_instances[$name])) {
            self::$_instances[$name] = new Session2();
            self::$_instances[$name]->setInstanceName($name);
            self::$_instances[$name]->start();

            if (empty($_SESSION['__flash__count'])) {
                $_SESSION['__flash__'] = array();    
            } else {
                $_SESSION['__flash__count']--;
            }
            

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
        //    session_start();


        /*
        $servers = explode(",", ini_get("session.save_path"));
        $c = count($servers);
        for ($i = 0; $i < $c; ++$i) {
            $servers[$i] = explode(":", $servers[$i]);
        }
        $memcached = new \Memcached();
        call_user_func_array([ $memcached, "addServers" ], $servers);
        print_r($memcached->getAllKeys());
        */  /*
        $request = Request::createFromGlobals(); //new Request($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER);//
        $memcache = new Memcache;
        $memcache->connect('localhost', 11211);
        $storage = new NativeSessionStorage(array(), new MemcacheSessionHandler($memcache));
        echo $request->getSession();
        $session = new Session($storage);
      */

       //$session->start();
        //$session->set('name', 'Drak');
       // print_r($session->all());
       // print_r($_SESSION);




        return $this;
    }

    public function setParams($lifetime = 0, $path = '/', $domain = '', $secure = false, $httponly = false) 
    {
        session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
        
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
        session_destroy();

        unset(self::$_instances[$this->getInstanceName()]);

        return true;
    }

    public function setFlash($key, $value) 
    {
        $_SESSION['__flash__'][$key] = $value;
        $_SESSION['__flash__count'] = 1;

        return $this;
    }

    public function getFlash($key, $default = null)
    {
        if (isset($_SESSION['__flash__'][$key])) {
            return $_SESSION['__flash__'][$key];    
        }
        return $default;
    }

}
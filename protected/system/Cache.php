<?php
class Cache extends Memcache
{
    /**
    *
    * @var Memcache
    */
    private static $_instances = array();
    
    /**
    * Create a memcache connection and return it
    *
    * @param string $name
    * @param array $connection_properties ['host', 'port', 'timeout', 'persistent']
    *
    *
    * @return Memcache;
    */
    public static function init($name = 'default', $connection_properties = array()) {
        if (!isset(self::$_instances[$name]) || !self::$_instances[$name] instanceof Memcache) {
        // try to create new connection
            if ($connection_properties['host'] && $connection_properties['port']) {
                $mcache = new Memcache();
                $connect_function = empty($connection_properties['persistent']) || $connection_properties['persistent'] == false ? 'connect' : 'pconnect';
                if (!$mcache->$connect_function($connection_properties['host'], $connection_properties['port'], $connection_properties['timeout'])) {
                    throw new CacheException('Unable to connect cache server');
                }
                self::$_instances[$name] = $mcache;
            } else {
                throw new CacheException('Missing connection properties');
            }
        }
        return self::$_instances[$name];
    }
}

class CacheException extends Exception
{
}
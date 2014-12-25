<?php
class DB {
    
    /**
     * 
     * @var PDO
     */
    private static $_instances = array();
    
    /**
     * Create a PDO connection and return it
     * 
     * @param string $name
     * @param array $connection_properties ['dsn', 'user', 'password', 'options'] 
     *
     * 
     * @return PDO;
     */
    public static function Connect($name = 'default', $connection_properties = array()) {
        if (!isset(self::$_instances[$name]) || !self::$_instances[$name] instanceof PDO) {
            // try to create new connection
            if ($connection_properties['dsn'] &&
                $connection_properties['user']) {
                try {
                    $pdo = new \PDO($connection_properties['dsn'], $connection_properties['user'], $connection_properties['password'], @$connection_properties['options']);
                    self::$_instances[$name] = $pdo;
                } catch (\PDOException $e) {
                    throw new DBExeption($e->getMessage());
                }
            } else {
                throw new DBExeption('Missing connection properties');
            }
        }
        return self::$_instances[$name];
    }

    public static function Reconnect($name = 'default', $connection_properties = array()) {
            // try to force create new connection
            if ($connection_properties['dsn'] &&
                $connection_properties['user']) {
                try {
                    $pdo = new \PDO($connection_properties['dsn'], $connection_properties['user'], $connection_properties['password'], @$connection_properties['options']);
                    self::$_instances[$name] = $pdo;
                } catch (\PDOException $e) {
                    throw new DBExeption($e->getMessage());
                }
            } else {
                throw new DBExeption('Missing connection properties');
            }

        return self::$_instances[$name];
    }

}

class DBExeption extends \Exception {}

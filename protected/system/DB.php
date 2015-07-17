<?php
class DB {
    
    /**
     * 
     * @var PDO
     */
    private static $_instances = array();
    private static $counter = 0;
    private static $times = 10;
    
    /**
     * Create a PDO connection and return it
     * 
     * @param string $name
     * @param array $connection_properties ['dsn', 'user', 'password', 'options'] 
     *
     * 
     * @return PDO;
     */
    public static function Connect($name = 'default', $connection_properties = array(), $reconnect = false) {

        if ($reconnect || !isset(self::$_instances[$name]) || !self::$_instances[$name] instanceof PDO) {

            // try to create new connection
            if ($connection_properties['dsn'] &&
                $connection_properties['user']) {
                try {

                    $pdo = new \PDO($connection_properties['dsn'], $connection_properties['user'], $connection_properties['password'], @$connection_properties['options']);
                    self::$_instances[$name] = $pdo;

                } catch (\PDOException $e) {

                    if($reconnect && self::$counter < self::$times ){
                        self::$counter ++;
                        echo "TRY RECONNECT\n";
                        sleep(1);
                        self::Connect($name, $connection_properties, true);
                    } else {
                        self::$counter = 0;
                        throw new DBExeption($e->getMessage());
                    }
                }

            } else {
                throw new DBExeption('Missing connection properties');
            }
        }
        return self::$_instances[$name];
    }

}

class DBExeption extends \Exception {}

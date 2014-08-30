<?php

Config::instance()->cacheEnabled = true;
Config::instance()->dbConnectionProperties = array(
    'dsn' => 'mysql:host=localhost;dbname=lotzone',
    'user' => 'root',
    'password' => '',
    'options' => array(
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
    ),
);  
Config::instance()->cacheConnectionProperties = array(
    'host'       => 'localhost',
    'port'       => 11211,
    'timeout'    => 1,
    'persistent' => true
);


// init memcache connection
try {
    Cache::init('default', Config::instance()->cacheConnectionProperties);    
} catch (CacheException $e) {
    Config::instance()->cacheEnabled = false;
}

// init database connection
DB::Connect('default', Config::instance()->dbConnectionProperties);
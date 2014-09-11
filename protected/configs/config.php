<?php

Config::instance()->cacheEnabled = true;
Config::instance()->newsCacheCount = 18;

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

Config::instance()->langs = array('ua', 'ru', 'en');
Config::instance()->defaultLang = 'ru';

// init memcache connection
try {
    Cache::init('default', Config::instance()->cacheConnectionProperties);    
} catch (CacheException $e) {
    Config::instance()->cacheEnabled = false;
}

// init database connection
DB::Connect('default', Config::instance()->dbConnectionProperties);

Config::instance()->privateResources =  array(
    '/private/' => 'controllers\admin\Game:index',
    '/private/login/' => array(
        'get'  => 'controllers\admin\Login:index',
        'post' => 'controllers\admin\Login:auth'
    ),
    '/private/logout/' => 'controllers\admin\Login:logout',
    '/private/game/' => array(
        'get'  => 'controllers\admin\Game:index',
        'post' => 'controllers\admin\Game:save',
    ),
    '/private/game/addcountry' => array(
        'post' => 'controllers\admin\Game:addcountry',
    ),
    '/private/admins/' => array(
        'get'   => 'controllers\admin\Admins:index',
        'post'  => 'controllers\admin\Admins:create',
    ),
    '/private/admins/:login' => array(
        'get'    => 'controllers\admin\Admins:details',
        'put'    => 'controllers\admin\Admins:update',
        'delete' => 'controllers\admin\Admins:delete',
    ),
    '/private/texts/' => array(
        'get'    => 'controllers\admin\Texts:index',
        'post'   => 'controllers\admin\Texts:save',
    ),
    '/private/texts/:identifier' => array(
        'delete'    => 'controllers\admin\Texts:delete',
    ),
    '/private/news/' => array(
        'get'    => 'controllers\admin\News:index',
    ),
    '/private/news/:lang' => array(
        'get'    => 'controllers\admin\News:index',
        'post'   => 'controllers\admin\News:save',
    ),
    '/private/news/:identifier' => array(
        'delete'    => 'controllers\admin\News:delete',
    ),
);
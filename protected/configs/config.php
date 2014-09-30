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

Config::instance()->langs = array('UA', 'RU', 'EN');
Config::instance()->defaultLang = 'UA';
Config::instance()->langCurrencies = array(
    'UA' => 'грн',
    'RU' => 'руб',
    'EN' => 'usd',
);

// init memcache connection
try {
    Cache::init('default', Config::instance()->cacheConnectionProperties);    
} catch (CacheException $e) {
    Config::instance()->cacheEnabled = false;
}

// init database connection
DB::Connect('default', Config::instance()->dbConnectionProperties);

Config::instance()->errorMessages = array(
    'AGREE_WITH_RULES' => 'Вы должны ознакомится с правилами',
    'EMPTY_EMAIL'      => 'Введите email',
    'INVALID_EMAIL'    => 'Неверный email',
    'REG_LOGIN_EXISTS' => 'Этот email уже зарегистрирован',
    'EMPTY_PASSWORD'   => 'Введите пароль',
    'PLAYER_NOT_FOUND' => 'Учетная запись не найдена',
    'INVALID_PASSWORD' => 'Неверный пароль',
);

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
    '/private/shop/' => array(
        'get'    => 'controllers\admin\Shop:index',
    ),
    '/private/shop/category/:id' => array(
        'get'    => 'controllers\admin\Shop:index',
    ),
    '/private/shop/addCategory/' => array(
        'post'    => 'controllers\admin\Shop:addCategory',
    ),
    '/private/shop/deleteCategory/' => array(
        'delete'    => 'controllers\admin\Shop:deleteCategory',
    ),
    '/private/shop/uploadPhoto' => array(
        'post'    => 'controllers\admin\Shop:uploadPhoto',
    ),
    '/private/shop/item' => array(
        'post'    => 'controllers\admin\Shop:addItem',
        'delete'  => 'controllers\admin\Shop:deleteItem',
    ),
    '/private/users'        => 'controllers\admin\ComingSoon:index',
    '/private/banners'      => 'controllers\admin\ComingSoon:index',
    '/private/monetisation' => 'controllers\admin\ComingSoon:index',
    '/private/ogames'       => 'controllers\admin\ComingSoon:index',
    '/private/stats'        => 'controllers\admin\ComingSoon:index',
);

Config::instance()->publicResources = array(
    '/' => 'controllers\production\Index:index',
    '/players/register/' => array(
        'post'  => 'controllers\production\Players:register',
    ),
    '/players/login/' => array(
        'post'  => 'controllers\production\Players:login',
    ),
    '/players/logout/' => 'controllers\production\Players:logout',
    '/players/update/' => array(
        'post'  => 'controllers\production\Players:update',
    ),
    '/game/ticket/' => array(
        'post'  => 'controllers\production\Game:createTicket',
    ),
);

Config::instance()->defaultSenderEmail = 'ravanger@kntele.com';
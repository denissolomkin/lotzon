<?php

Config::instance()->cacheEnabled = false;
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

Config::instance()->langs = array('UA', 'RU', 'EN', 'BY');
Config::instance()->countryLangs = array(
    'UA' => 'UA',
    'RU' => 'UA',
    'BY' => 'UA',
);
Config::instance()->defaultLang = 'RU';
Config::instance()->langCurrencies = array(
    'UA' => 'грн',
    'RU' => 'руб',
    'BY' => 'руб',
    'EN' => 'usd',
);

// init memcache connection
try {
    if (Config::instance()->cacheEnabled) {
        Cache::init('default', Config::instance()->cacheConnectionProperties);        
    }
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
    'ALREADY_INVITED'  => 'На этот email уже было отправлено приглашение',
    'EMAIL_NOT_VALIDATED' => 'Завершите процесс регистрации через свой email.',
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
    '/private/shop/renameCategory/' => array(
        'post'    => 'controllers\admin\Shop:renameCategory',
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
    '/private/shop/updateItem/' => array(
        'post'    => 'controllers\admin\Shop:updateItem',
    ),
    '/private/chances' => array(
        'get' => 'controllers\admin\MomentalChances:index',
        'post' => 'controllers\admin\MomentalChances:save',
    ),
    '/private/seo' => array(
        'get' => 'controllers\admin\SEO:index',
        'post' => 'controllers\admin\SEO:save',
    ),
    '/private/comments' => array(
        'get' => 'controllers\admin\Comments:index',
        'post' => 'controllers\admin\Comments:save',
    ),
    '/private/comments/:id/delete' => array(
        'get' => 'controllers\admin\Comments:delete',
    ),
    '/private/monetisation' => 'controllers\admin\Monetisation:index',
    '/private/monetisation/approve/:id' => 'controllers\admin\Monetisation:approve',
    '/private/monetisation/decline/:id' => 'controllers\admin\Monetisation:decline',

    '/private/users'        => 'controllers\admin\Users:index',
    '/private/users/stats/:playerId' => 'controllers\admin\Users:stats',
    '/private/banners'      => 'controllers\admin\ComingSoon:index',
    '/private/ogames'       => 'controllers\admin\ComingSoon:index',
    '/private/ogames'       => 'controllers\admin\ComingSoon:index',
    '/private/subscribes'   => 'controllers\admin\Subscribes:index',

);

Config::instance()->publicResources = array(
    '/' => 'controllers\production\Index:index',
    '/vkproxy/' => 'controllers\production\Index:VKProxy',
    '/feedback/' => 'controllers\production\Index:feedback',
    '/trailer/' => array(
        'get'   => 'controllers\production\TrailerController:index',
        'post'  => 'controllers\production\TrailerController:subscribe'
    ),
    '/stats/promo/' => 'controllers\production\Index:stats',
    '/players/register/' => array(
        'post'  => 'controllers\production\Players:register',
    ),

    '/players/resendPassword/' => array(
        'post'  => 'controllers\production\Players:resendPassword',  
    ),
    '/players/login/' => array(
        'post'  => 'controllers\production\Players:login',
    ),
    '/players/logout/' => 'controllers\production\Players:logout',
    '/players/social/' => 'controllers\production\Players:social',
    '/players/update/' => array(
        'post'  => 'controllers\production\Players:update',
    ),
    '/players/updateAvatar' => array(
        'post'  => 'controllers\production\Players:saveAvatar',
        'delete' => 'controllers\production\Players:removeAvatar',
    ),
    '/players/ping' => 'controllers\production\Players:ping',
    '/game/ticket/' => array(
        'post'  => 'controllers\production\Game:createTicket',
    ),
    '/game/lastLottery/'  => 'controllers\production\Game:lastLottery',
    '/content/lotteries/' => 'controllers\production\ContentController:lotteries',
    '/content/shop/'      => 'controllers\production\ContentController:shop',
    '/content/news/'      => 'controllers\production\ContentController:news',
    '/order/item/'        => 'controllers\production\OrdersController:orderItem',
    '/content/lottery/:lotteryId' => 'controllers\production\ContentController:lotteryDetails',
    '/content/lottery/next/:lotteryId' => 'controllers\production\ContentController:nextLotteryDetails',
    '/content/lottery/prev/:lotteryId' => 'controllers\production\ContentController:prevLotteryDetails',
    '/content/transactions/:currency/'  => 'controllers\production\ContentController:transactions',

    '/invites/email' => 'controllers\production\InvitesController:emailInvite',
    '/chance/build/:identifier' => array(
        'get' => 'controllers\production\Game:startChanceGame',
    ),
    '/chance/play/:identifier' => array(
        'post' => 'controllers\production\Game:chanceGamePlay',
    ),
);

Config::instance()->defaultSenderEmail = 'no-reply@lotzon.com';
Config::instance()->playerOfflineTimeout = 5 * 60;
Config::instance()->generatorNumTries = 5;

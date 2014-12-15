<?php

Config::instance()->cacheEnabled = false;
Config::instance()->newsCacheCount = 18;

Config::instance()->dbConnectionProperties = array(
    // testbed
    /**/    'dsn' => 'mysql:host=127.0.0.1;dbname=lotzon_testbed',
        'user' => 'testbed',
        'password' => '2p9G808CVn17P',
    // public
    /*    'dsn' => 'mysql:host=127.0.0.1;dbname=lotzone',
    'user' => 'lotzone_user',
    'password' => '63{_Tc252!#UoQq',
    */
    // local
    /*    'dsn' => 'mysql:host=localhost;dbname=lotzone',
        'user' => 'root', #test
        'password' => '1234',
    */
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
    'BLOCKED_EMAIL_DOMAIN' => 'Регистрация с этого email-домена запрещена',
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
    '/private/users/rmTransaction/:trid' => array(
        'post' => 'controllers\admin\Users:removeTransaction'
    ),
    '/private/users/addTransaction/:playerId' => array(
        'post' => 'controllers\admin\Users:addTransaction'
    ),
    '/private/users/transactions/:playerId' => 'controllers\admin\Users:transactions',
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
    '/players/login/vk/' => array(
        'get' => 'controllers\production\Players:loginVk',
    ),
    '/auth/:provider' => 'controllers\production\AuthController:auth',
    '/auth/endpoint/' => 'controllers\production\AuthController:endpoint',
    '/ws/run/'      => 'controllers\production\WebSocketServer:run',
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
    '/order/money/'       => 'controllers\production\OrdersController:orderMoney',
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

Config::instance()->hybridAuth = array(

        // "base_url" the url that point to HybridAuth Endpoint (where the index.php and config.php are found)
        "base_url" => "http://testbed.lotzon.com/auth/endpoint/",

        "providers" => array (

            "Twitter" => array (
                "enabled" => true,
                "keys" => array ( "key" => "q8MuZoLMf02gd5lcDIcntWgsq", "secret" => "hO5NPBiiiJqrDIk57jJzLZYH65R24phFi0lxISWvKiUqL6Rsx8" )
            ),

            "Google" => array (
                "enabled" => true,
                "keys" => array ( "id" => "355196749921-66tdkn697420i6rbji3decd394ru8dsv.apps.googleusercontent.com", "secret" => "WkXAp8ky-R7zuEx565Bwdnji" ),
                'scope' => 'email'
            ),

            "Odnoklassniki" => array (
                "enabled" => true,
                "keys" => array ( "id" => "1112461056", "secret" => "149DA72E626CF26CFCC436A3" ),
                'scope' => 'email'),

            "Vkontakte" => array (
                "enabled" => true,
                "keys" => array ( "id" => "4674779", "secret" => "uiFFV2KDYUbH6z9SSyi4" ),
                'scope' => 'email'
            ),

            "Facebook" => array (
                "enabled" => true,
                "keys" => array ( "id" => "571381022994041", "secret" => "2a59f655677472049ebf12ef95f489bc" ),
                'scope' => 'email'
            )
        )
    );

Config::instance()->vkCredentials = array(
    'appId'        => '4617228',
    'secret'       => 'hbTNQKCHQ03tk5XLISmy',
    'redirectUrl' => 'http://lotzon.com/players/login/vk?redirected=1',
    'scope'        => 'email',
);

Config::instance()->blockedEmails = array(
    'trbvm.com', 'tempinbox.com', 'mailinator.com', 'sharklasers.com',
    'grr.la', 'guerrillamail.biz', 'guerrillamail.com', 'guerrillamail.de',
    'guerrillamail.net', 'guerrillamail.org', 'guerrillamailblock.com', 'spam4.me',
    'mt2014.com', 'mailmetrash.com', 'trashymail.com', 'mt2009.com', 'trash2009.com',
    'thankyou2010.com', 'thankyou2010.com', 'TempEMail.net', 'bigprofessor.so',
    'alivance.com', 'lackmail.net',
    'walkmail.net','yopmail.com','mailspeed.ru',
    'sharklasers.com', 'yopmail.fr','yopmail.net','cool.fr.nf','jetable.fr.nf',
    'nospam.ze.tc','nomail.xl.cx','mega.zik.dj', 'gustr.com', 'fleckens.hu',
    'speed.1s.fr','courriel.fr.nf','moncourrier.fr.nf','monemail.fr.nf','monmail.fr.nf','tryalert.com',
    'sofimail.com','einrot.com','armyspy.com','cuvox.de','dayrep.com','hulapla.de','meltmail.com',
    'anonymbox.com','discardmail.com','mail-temporaire.fr','trashmail.com','filzmail.com',
    'dunflimblag.mailexpire.com', 'mailcatch.com', 'discardmail.de', 'dispostable.com',
    'ce.mintemail.com', 'spambog.com', 'spamfree24.org', 'spambog.de', 'mailnull.com',
    'mytempemail.com', 'incognitomail.com', 'spamobox.com', 'deadaddress.com', 'uroid.com',
    'spambog.ru', 'mailscrap.com', 'cachedot.net', 'onewaymail.com', 'get2mail.fr', 'mynetstore.de',
    '0815.ru', 'fakeinbox.com', 'teleworm.us', 'yomail.info', 'maildrop.cc', 'voidbay.com', 'mailnesia.com',
    'mytemp.email', 'tempsky.com', 'mohmal.com', 'forward.cat', 'cnmsg.net', 'explodemail.com',
    'emailsensei.com', 'nowmymail.com', 'crapmail.org', 'shitmail.org', 'eyepaste.com', 'hideme.be',
    'q314.net', 'one-time.email', 'emailgo.de', 'squizzy.de', 'tempmailer.de', 'kurzepost.de',
    'objectmail.com', 'proxymail.eu', 'rcpt.at', 'trash-mail.at', 'trashmail.at', 'trashmail.me',
    'trashmail.net', 'wegwerfmail.de', 'wegwerfmail.net', 'wegwerfmail.org', 'jourrapide.com'
);

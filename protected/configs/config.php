<?php

Config::instance()->cacheConnectionProperties = array(
    'host'       => 'localhost',
    'port'       => 11211,
    'timeout'    => 1,
    'persistent' => true
);

// init memcache connection
try {
    if (Config::instance()->cacheEnabled)
        Cache::init('default', Config::instance()->cacheConnectionProperties);
} catch (CacheException $e) {
    Config::instance()->cacheEnabled = false;
}

// init database connection
try {
    DB::Connect('default', Config::instance()->dbConnectionProperties);
} catch (\EntityException $e) {}


Config::instance()->defaultSenderEmail = 'no-reply@lotzon.com';

/*
Config::instance()->newsCacheCount = 18;
Config::instance()->playerOfflineTimeout = 5 * 60;
Config::instance()->generatorNumTries = 20;

Config::instance()->langs = array('RU', 'UA', 'EN');
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
Config::instance()->errorMessages = array(
    'AGREE_WITH_RULES' => 'Вы должны ознакомиться с правилами',
    'EMPTY_EMAIL'      => 'Введите email',
    'INVALID_EMAIL'    => 'Неверный email',
    'REG_LOGIN_EXISTS' => 'Этот email уже зарегистрирован',
    'EMPTY_PASSWORD'   => 'Введите пароль',
    'PLAYER_NOT_FOUND' => 'Учетная запись не найдена',
    'INVALID_PASSWORD' => 'Неверный пароль',
    'ALREADY_INVITED'  => 'На этот email уже было отправлено приглашение',
    'EMAIL_NOT_VALIDATED' => 'Завершите процесс регистрации через свой email',
    'BLOCKED_EMAIL_DOMAIN' => 'Регистрация с этого email-домена запрещена',
    'BLOCKED_IP'        => 'Регистрация пользователя запрещена',
    'ACCESS_DENIED'        => 'Доступ запрещен'
);
*/

Config::instance()->privateResources =  array(
    '/private/' => 'controllers\admin\Users:index',
    '/private/login/' => array(
        'get'  => 'controllers\admin\Login:index',
        'post' => 'controllers\admin\Login:auth'
    ),
    '/private/logout/' => 'controllers\admin\Login:logout',
    '/private/lottery/' => array(
        'get'  => 'controllers\admin\Lottery:index',
        'post' => 'controllers\admin\Lottery:save',
    ),
    '/private/countries/' => array(
        'get'  => 'controllers\admin\Countries:index',
        'post' => 'controllers\admin\Countries:save',
    ),
    '/private/currencies/' => array(
        'get'  => 'controllers\admin\Currencies:index',
        'post' => 'controllers\admin\Currencies:save',
    ),
    '/private/languages/' => array(
        'get'  => 'controllers\admin\Languages:index',
        'post' => 'controllers\admin\Languages:save',
    ),
    '/private/lottery/simulation' => array(
        'post' => 'controllers\admin\Lottery:simulation',
    ),
    '/private/lottery/addcountry' => array(
        'post' => 'controllers\admin\Lottery:addcountry',
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
    '/private/statictexts/' => array(
        'get'    => 'controllers\admin\StaticTexts:index',
        'post'   => 'controllers\admin\StaticTexts:save',
    ),

    '/private/statictexts/:identifier' => array(
        'get'    => 'controllers\admin\StaticTexts:get',
        'delete'    => 'controllers\admin\StaticTexts:delete',
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
    '/private/qgames' => array(
        'get' => 'controllers\admin\QuickGames:index',
        'post' => 'controllers\admin\QuickGames:save',
    ),
    '/private/ogames' => array(
        'get' => 'controllers\admin\OnlineGames:index',
        'post' => 'controllers\admin\OnlineGames:save',
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
    '/private/results' => 'controllers\admin\LotteryResult:index',

    '/private/reviews/' => 'controllers\admin\Reviews:index',
    '/private/reviews/status/:id' => 'controllers\admin\Reviews:status',
    '/private/reviews/delete/:id' => 'controllers\admin\Reviews:delete',

    '/private/monetisation' => 'controllers\admin\Monetisation:index',
    '/private/monetisation/status/:id' => 'controllers\admin\Monetisation:status',

    '/private/users'        => 'controllers\admin\Users:index',
    '/private/users/profile/:playerId' => array(
        'get' => 'controllers\admin\Users:profile',
        'post' => 'controllers\admin\Users:updateProfile'
    ),
    '/private/users/stats/:playerId' => 'controllers\admin\Users:stats',
    '/private/users/rmNotice/:trid' => array(
        'post' => 'controllers\admin\Users:removeNotice'
    ),
    '/private/users/addNotice/:playerId' => array(
        'post' => 'controllers\admin\Users:addNotice'
    ),
    '/private/users/notices/:playerId' => 'controllers\admin\Users:notices',
    '/private/users/rmNote/:trid' => array(
        'post' => 'controllers\admin\Users:removeNote'
    ),
    '/private/users/addNote/:playerId' => array(
        'post' => 'controllers\admin\Users:addNote'
    ),
    '/private/users/notes/:playerId' => 'controllers\admin\Users:notes',
    '/private/users/delete/:playerId' => 'controllers\admin\Users:delete',
    '/private/users/ban/:playerId' => 'controllers\admin\Users:ban',
    '/private/users/logs/:playerId' => 'controllers\admin\Users:logs',
    '/private/users/reviews/:playerId' => 'controllers\admin\Users:reviews',
    '/private/users/logins/:playerId' => 'controllers\admin\Users:logins',
    '/private/users/orders/:playerId' => 'controllers\admin\Users:orders',
    '/private/users/tickets/:playerId' => 'controllers\admin\Users:tickets',
    '/private/users/rmTransaction/:trid' => array(
        'post' => 'controllers\admin\Users:removeTransaction'
    ),
    '/private/users/addTransaction/:playerId' => array(
        'post' => 'controllers\admin\Users:addTransaction'
    ),
    '/private/users/transactions/:playerId' => 'controllers\admin\Users:transactions',
    '/private/banners/'      => array(
        'get' => 'controllers\admin\Banners:index',
        'post' => 'controllers\admin\Banners:save',
    ),
    '/private/banner/'      => array(
              'post' => 'controllers\admin\Banners:banner',
    ),
    '/private/bonuses/' => array(
        'get'  => 'controllers\admin\Bonuses:index',
        'post' => 'controllers\admin\Bonuses:save',
    ),
    '/private/counters/' => array(
        'get'  => 'controllers\admin\Counters:index',
        'post' => 'controllers\admin\Counters:save',
    ),
    '/private/blacklist/'      => array(
        'get' => 'controllers\admin\Blacklist:index',
        'post' => 'controllers\admin\Blacklist:save',
    ),
    '/private/rights/'      => array(
        'get' => 'controllers\admin\Rights:index',
        'post' => 'controllers\admin\Rights:save',
    ),
    '/private/gamebots/'      => array(
        'get' => 'controllers\admin\GameBots:index',
        'post' => 'controllers\admin\GameBots:save',
    ),
    '/private/gamebots/uploadPhoto' => array(
        'post'    => 'controllers\admin\GameBots:uploadPhoto',
    ),
    '/private/games/'      => array(
        'get' => 'controllers\admin\Games:index',
        'post' => 'controllers\admin\Games:save',
    ),
    '/private/partners/' => array(
        'get' => 'controllers\admin\Partners:index',
        'delete' => 'controllers\admin\Partners:delete',
        'post' => 'controllers\admin\Partners:save',
    ),
    '/private/images/' => array(
        'get' => 'controllers\admin\Images:index',
        'delete' => 'controllers\admin\Images:delete',
        'post' => 'controllers\admin\Images:upload',
    ),
    '/private/audio/' => array(
        'post' => 'controllers\admin\Images:audio',
    ),
    '/private/gamestats/'      => 'controllers\admin\ComingSoon:index',
    '/private/subscribes'   => 'controllers\admin\Subscribes:index',
);

Config::instance()->publicResources = array(
    '/' => 'controllers\production\Index:index',
    '/:page' => 'controllers\production\Index:index',
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
    '/players/trouble/:trouble' => 'controllers\production\Players:trouble',
    '/players/disableSocial/:provider' => 'controllers\production\Players:disableSocial',
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
    '/content/lotteries/' => 'controllers\production\ContentController:lotteries',
    '/content/shop/'      => 'controllers\production\ContentController:shop',
    '/content/banner/:sector'      => 'controllers\production\ContentController:banner',
    '/content/news/'      => 'controllers\production\ContentController:news',
    '/content/reviews/'      => 'controllers\production\ContentController:reviews',
    '/order/item/'        => 'controllers\production\OrdersController:orderItem',
    '/order/money/'       => 'controllers\production\OrdersController:orderMoney',
    '/review/save/'        => 'controllers\production\ReviewsController:save',
    '/review/uploadImage/'       => 'controllers\production\ReviewsController:uploadImage',
    '/review/removeImage/'       => 'controllers\production\ReviewsController:removeImage',
    '/content/lottery/:lotteryId' => 'controllers\production\ContentController:lotteryDetails',
    '/content/lottery/next/:lotteryId' => 'controllers\production\ContentController:nextLotteryDetails',
    '/content/lottery/prev/:lotteryId' => 'controllers\production\ContentController:prevLotteryDetails',
    '/content/transactions/:currency/'  => 'controllers\production\ContentController:transactions',
    '/content/notices/'  => 'controllers\production\ContentController:notices',

    '/invites/email' => 'controllers\production\InvitesController:emailInvite',
    '/language/:lang' => 'controllers\production\Players:changeLanguage',
    '/chance/build/:identifier' => array(
        'get' => 'controllers\production\Game:startChanceGame',
    ),
    '/chance/play/:identifier' => array(
        'post' => 'controllers\production\Game:chanceGamePlay',
    ),
    '/quickgame/build/:key' => array(
        'get' => 'controllers\production\Game:startQuickGame',
    ),
    '/quickgame/play/:key' => array(
        'post' => 'controllers\production\Game:playQuickGame',
    ),
    '/quickgame/preview/:key' => array(
        'post' => 'controllers\production\Game:previewQuickGame',
    ),
);

Config::instance()->hybridAuth = array(

    // "base_url" the url that point to HybridAuth Endpoint (where the index.php and config.php are found)
    "base_url" => "http://".(isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'lotzon.com')."/auth/endpoint/",

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
            "keys" => array ( "id" => "1117952512", "secret" => "D721378D1D8978B3F0918327", "key"=>"CBALJDKDEBABABABA" ),
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

/*
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
*/

/*
// init config from DB
$sth = DB::Connect()->prepare("SELECT * FROM `Config`");
$sth->execute();
if ($sth->rowCount())
    foreach ($sth->fetchAll() as $config)
        Config::instance()->$config['Key']=unserialize($config['Value']);
*/
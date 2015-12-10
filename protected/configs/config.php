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
    '/private/lottery/checkLock' => array(
        'post' => 'controllers\admin\Lottery:checkLock',
    ),
    '/private/lottery/force' => array(
        'post' => 'controllers\admin\Lottery:force',
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

    '/private/reviews/'       => array(
        'get' => 'controllers\admin\Reviews:index',
        'post' => 'controllers\admin\Reviews:save',
    ),
    '/private/reviews/list/:id' => 'controllers\admin\Reviews:list',
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
    '/private/users/mults/:playerId' => 'controllers\admin\Users:mults',
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
    '/private/reports/:identifier' => array(
        'get' => 'controllers\admin\Reports:index',
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
    '/private/gametop/'      => array(
        'get' => 'controllers\admin\GameTop:index',
        'post' => 'controllers\admin\GameTop:save',
    ),
    '/private/gametop/getPlayer/:playerId' => array(
        'get' => 'controllers\admin\GameTop:getPlayer'
    ),
    '/private/gametop/delete/:id' => array(
        'get' => 'controllers\admin\GameTop:delete'
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
    /**
     * maillist
     */
    '/private/maillist/' => array(
        'get' => 'controllers\admin\Maillist:listTasks',
    ),
    '/private/maillist/tasks' => array(
        'get'  => 'controllers\admin\Maillist:listTasks',
        'post' => 'controllers\admin\Maillist:saveTask',
    ),
    '/private/maillist/messages' => array(
        'get'    => 'controllers\admin\Maillist:listMessages',
        'post'   => 'controllers\admin\Maillist:saveMessage',
    ),
    '/private/maillist/messages/:identifier' => array(
        'get'    => 'controllers\admin\Maillist:getMessage',
        'delete' => 'controllers\admin\Maillist:deleteMessage',
    ),
    '/private/maillist/tasks/:identifier' => array(
        'get'    => 'controllers\admin\Maillist:getTask',
        'delete' => 'controllers\admin\Maillist:deleteTask',
    ),
    '/private/maillist/template/:identifier' => array(
        'get'    => 'controllers\admin\Maillist:getTemplatePreview',
    ),
    '/private/maillist/tasks/filter/' => array(
        'post'    => 'controllers\admin\Maillist:getTaskFilterCount',
    ),
    '/private/maillist/tasks/statistic/player_games/:identifier' => array(
        'get'    => 'controllers\admin\Maillist:getTaskStatisticPlayerGames',
    ),
    /**
     * linkredirect
     */
    '/private/linkredirect/' => array(
        'get'  => 'controllers\admin\LinkRedirectController:getLink',
        'post' => 'controllers\admin\LinkRedirectController:postLink',
    ),
    /**
     * Blog
     */
    '/private/blogs/'            => array(
        'get' => 'controllers\admin\Blogs:index',
    ),
    '/private/blogs/:lang'       => array(
        'get'  => 'controllers\admin\Blogs:index',
        'post' => 'controllers\admin\Blogs:save',
    ),
    '/private/blogs/:identifier' => array(
        'delete' => 'controllers\admin\Blogs:delete',
    ),
    '/private/blogs/uploadPhoto/' => array(
        'post' => 'controllers\admin\Blogs:uploadPhoto',
    ),
);

Config::instance()->publicResources = array(
    '/' => 'controllers\production\Index:index',
    '/:page/' => 'controllers\production\Index:index',
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
    '/players/social/:provider' => 'controllers\production\Players:social',
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
    '/unsubscribe/' => array(
        'get'  => 'controllers\production\Maillist:unsubscribe',
        'post' => 'controllers\production\Maillist:doUnsubscribe',
    ),
    '/lnk/:uin' => array(
        'get'  => 'controllers\production\LinkRedirectController:getLink',
    ),
    /**
     * Chance games
     */
    '/games/chance/'                          => array(
        'get' => 'controllers\production\ChanceController:list',
    ),
    '/games/chance/:id'                       => array(
        'get'  => array(
            'controllers\production\ChanceController:item',
            function ($obj) {
                $obj->setParams(array(
                        "key"      => "ChanceGame",
                        "objectId" => $obj->getParams()['id']
                    )
                );
            }
        ),
        'post' => 'controllers\production\ChanceController:start',
        'put'  => 'controllers\production\ChanceController:play',
    ),
    /**
     * Comments
     */
    '/communication/comments/' => array(
        'get'  => 'controllers\production\CommentsController:list',
        'post' => 'controllers\production\CommentsController:create'
    ),
    '/communication/comments/:commentId/like' => array(
        'post'   => 'controllers\production\CommentsController:like',
        'delete' => 'controllers\production\CommentsController:dislike'
    ),
    '/communication/comments/:commentId' => array(
        'get' => 'controllers\production\CommentsController:item'
    ),
    '/communication/notifications' => array(
        'get'    => 'controllers\production\CommentsController:notifications',
        'delete' => 'controllers\production\CommentsController:deleteNotifications'
    ),
    /**
     * Messages
     */
    '/communication/messages/' => array(
        'get'  => 'controllers\production\MessagesController:index',
        'post' => 'controllers\production\MessagesController:create'
    ),
    '/users/:userid/messages' => array(
        'get'  => 'controllers\production\MessagesController:list'
    ),
    /**
     * Blog
     */
    '/blog/posts/' => array(
        'get' => 'controllers\production\BlogsController:list'
    ),
    '/blog/post/:identifier' => array(
        'get' => 'controllers\production\BlogsController:post'
    ),
    '/blog/post/:identifier/comments' => array(
        'get'  => array(
            'controllers\production\CommentsController:list',
            function ($obj) {
                $obj->setParams(array(
                        "module"   => "blog",
                        "objectId" => $obj->getParams()['identifier']
                    )
                );
            }
        ),
        'post'  => array(
            'controllers\production\CommentsController:create',
            function ($obj) {
                $obj->setParams(array(
                        "module"   => "blog",
                        "objectId" => $obj->getParams()['identifier']
                    )
                );
            }
        )
    ),
    '/blog/post/:identifier/comments/like' => array(
        'post'   => 'controllers\production\CommentsController:like',
        'delete' => 'controllers\production\CommentsController:dislike'
    ),
    '/blog/post/:blogId/similar' => array(
        'get' => 'controllers\production\BlogsController:similar'
    ),
    /**
     * Users
     */
    '/users/search/' => array(
        'get' => 'controllers\production\Players:search'
    ),
    '/users/:userId' => array(
        'get' => 'controllers\production\Players:card'
    ),
    '/(.*)/' => 'controllers\production\Index:index',
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

*/

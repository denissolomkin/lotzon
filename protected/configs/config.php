<?php

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
    '/private/fgames' => array(
        'get' => 'controllers\admin\FlashGames:index',
        'post' => 'controllers\admin\FlashGames:save',
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

    '/private/messages/' => 'controllers\admin\Messages:index',
    '/private/messages/list/:playerId/:toPlayerId' => 'controllers\admin\Messages:list',
    '/private/messages/approve/:id' => 'controllers\admin\Messages:approve',
    '/private/messages/delete/:id' => 'controllers\admin\Messages:delete',

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
    '/private/users/:playerId/ban/:status' => 'controllers\admin\Users:ban',
    '/private/users/:playerId/bot/:status' => 'controllers\admin\Users:bot',
    '/private/users/:playerId/logout/:status' => 'controllers\admin\Users:logout',
    '/private/users/:playerId/avatar' => 'controllers\admin\Users:avatar',
    '/private/users/logs/:playerId' => 'controllers\admin\Users:logs',
    '/private/users/reviews/:playerId' => 'controllers\admin\Users:reviews',
    '/private/users/messages/:playerId' => 'controllers\admin\Users:messages',
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
    '/private/captcha/'      => array(
        'get' => 'controllers\admin\Captcha:index',
        'post' => 'controllers\admin\Captcha:save',
    ),
    '/private/banners/'      => array(
        'get' => 'controllers\admin\Banners:index',
        'post' => 'controllers\admin\Banners:save',
    ),
    '/private/banner/'      => array(
        'post' => 'controllers\admin\Banners:banner',
    ),
    '/private/ad/'      => array(
        'get' => 'controllers\admin\Ad:index',
        'post' => 'controllers\admin\Ad:save',
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
    '/private/whitelist/'      => array(
        'get' => 'controllers\admin\Whitelist:index',
        'post' => 'controllers\admin\Whitelist:save',
    ),
    '/private/blacklist/'      => array(
        'get' => 'controllers\admin\Blacklist:index',
        'post' => 'controllers\admin\Blacklist:save',
    ),
    '/private/debug/'      => array(
        'get' => 'controllers\admin\Debug:index'
    ),
    '/private/moderators/'      => array(
        'get' => 'controllers\admin\Moderators:index',
        'post' => 'controllers\admin\Moderators:save',
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
        'post' => 'controllers\admin\GameTop:create',
        'put' => 'controllers\admin\GameTop:update',
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
    '/ping' => 'controllers\production\PingController:index',
    '/debug' => array(
        'post' => 'controllers\production\DebugController:add',
    ),
    '/stats/promo/' => 'controllers\production\Index:stats',
    /**
     * Authentication
     */
    '/players/register/' => array(
        'post'  => 'controllers\production\Players:register',
    ),
    '/players/resendPassword/' => array(
        'post'  => 'controllers\production\Players:resendPassword',
    ),
    '/players/resendEmail/' => array(
        'post'  => 'controllers\production\Players:resendEmail',
    ),
    '/players/login/' => array(
        'post'  => 'controllers\production\Players:login',
    ),
    '/players/logout/' => 'controllers\production\AuthController:logout',
    '/auth/logout/' => 'controllers\production\AuthController:logout',
    /**
     * Captcha
     */
    '/players/captcha/' => array(
        'post'  => 'controllers\production\Players:captcha',
    ),
    /**
     * Socials
     */
    '/auth/:provider' => 'controllers\production\AuthController:auth',
    '/auth/endpoint/' => 'controllers\production\AuthController:endpoint',
    '/players/trouble/:trouble' => 'controllers\production\Players:trouble',
    '/profile/social/' => array(
        'delete' => 'controllers\production\Players:disableSocial',
    ),
    /**
     * Socials refpost
     */
    '/players/social/:provider' => 'controllers\production\Players:socialPost',
    /**
     * Invites
     */
    '/invites/email' => 'controllers\production\InvitesController:emailInvite',
    /**
     * Subscribe
     */
    '/unsubscribe/' => array(
        'get'  => 'controllers\production\Maillist:unsubscribe',
        'post' => 'controllers\production\Maillist:doUnsubscribe',
    ),
    /**
     * Link Redirect
     */
    '/lnk/:uin' => array(
        'get'  => 'controllers\production\LinkRedirectController:getLink',
    ),
    /**
     * Banner
     */
    '/banner/:device/:location(/:page)' => 'controllers\production\BannersController:index',
    /**
     * Moment games
     */
    '/games/moment' => array(
        'get'  => array(
            'controllers\production\ChanceController:start',
            function ($route) {
                $route->setParams(array(
                        "key"   => "Moment"
                    )
                );
            }
        ),
        'put'  => array(
            'controllers\production\ChanceController:play',
            function ($route) {
                $route->setParams(array(
                        "key"   => "Moment"
                    )
                );
            }
        ),
    ),
    '/games/moment/play' => array(
        'get'  => array(
            'controllers\production\ChanceController:play',
            function ($route) {
                $route->setParams(array(
                        "key"   => "Moment"
                    )
                );
            }
        ),
    ),
    /**
     * Random games
     */
    '/games/random' => array(
        'get'  => array(
            'controllers\production\ChanceController:start',
            function ($route) {
                $route->setParams(array(
                        "key"   => "QuickGame"
                    )
                );
            }
        ),
        'put'  => array(
            'controllers\production\ChanceController:play',
            function ($route) {
                $route->setParams(array(
                        "key"   => "QuickGame"
                    )
                );
            }
        ),
    ),
    '/games/random/play' => array(
        'get'  => array(
            'controllers\production\ChanceController:play',
            function ($route) {
                $route->setParams(array(
                        "key"   => "QuickGame"
                    )
                );
            }
        ),
    ),
    /**
     * Chance games
     */
    '/games/chance/' => array(
        'get'  => array(
            'controllers\production\ChanceController:list',
            function ($route) {
                $route->setParams(
                    array("key" => "ChanceGame")
                );
            }
        ),
    ),
    '/games/chance/:id' => array(
        'get'  => array(
            'controllers\production\ChanceController:item',
            function ($route) {
                $route->setParams(array(
                        "key"   => "ChanceGame",
                        "id"    => $route->getParam('id')
                    )
                );
            }
        ),
        'post'  => array(
            'controllers\production\ChanceController:start',
            function ($route) {
                $route->setParams(array(
                        "key"   => "ChanceGame",
                        "id"    => $route->getParam('id')
                    )
                );
            }
        ),
        'put'  => array(
            'controllers\production\ChanceController:play',
            function ($route) {
                $route->setParams(array(
                        "key"   => "ChanceGame",
                        "id"    => $route->getParam('id')
                    )
                );
            }
        ),
    ),
    '/games/chance/:id/play' => array(
        'get'  => array(
            'controllers\production\ChanceController:play',
            function ($route) {
                $route->setParams(array(
                        "key"   => "ChanceGame",
                        "id"    => $route->getParam('id')
                    )
                );
            }
        ),
    ),
    /**
     * Slots games
     */
    '/games/slots/:id' => array(
        'get'  => array(
            'controllers\production\SlotsController:item',
            function ($route) {
                $route->setParams(array(
                        "key"   => "ChanceGame",
                        "id"    => $route->getParam('id')
                    )
                );
            }
        ),
        'post'  => array(
            'controllers\production\SlotsController:start',
            function ($route) {
                $route->setParams(array(
                        "key"   => "ChanceGame",
                        "id"    => $route->getParam('id')
                    )
                );
            }
        )
    ),
    /**
     * Flash
     */
    '/games/flash/' => array(
        'get'  => 'controllers\production\FlashController:list'
    ),
    '/games/flash/:id' => array(
        'get'   => 'controllers\production\FlashController:item'
    ),
    /**
     * Games
     */
    '/games/:key/' => array(
        'get'  => 'controllers\production\GamesController:list'
    ),
    '/games/:key/:id' => array(
        'get'   => 'controllers\production\GamesController:item',
        'post'  => 'controllers\production\GamesController:start',
        'put'   => 'controllers\production\GamesController:play'
    ),
    '/games/:key/:id/play' => array(
        'get'  => 'controllers\production\GamesController:play',
    ),
    '/games/:key/:id/rating' => array(
        'get'  => 'controllers\production\GamesController:rating',
    ),
    '/games/:key/:id/now' => array(
        'get'  => 'controllers\production\GamesController:now',
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
    '/communication/comments/:commentId/complain' => array(
        'post'   => 'controllers\production\CommentsController:complain',
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
        'get'    => 'controllers\production\MessagesController:list',
        'delete' => 'controllers\production\MessagesController:markRead',
        'post'   => 'controllers\production\MessagesController:markRead'
    ),
    '/image' => array(
        'post'   => 'controllers\production\ImagesController:message',
        'delete' => 'controllers\production\MessagesController:imageDelete'
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
     * Lottery
     */
    '/lottery/ticket'                     => array(
        'post' => 'controllers\production\LotteryController:createTicket',
    ),
    '/lottery/gold'                       => array(
        'post' => 'controllers\production\LotteryController:buyGoldTicket',
    ),
    '/lottery/history'                    => array(
        'get' => 'controllers\production\LotteryController:history',
    ),
    '/lottery/tickets' => array(
        'get' => 'controllers\production\LotteryController:tickets',
    ),
    '/lottery/:lotteryId'         => array(
        'get' => 'controllers\production\LotteryController:lotteryInfo',
    ),
    '/lottery/:lotteryId/tickets' => array(
        'get' => 'controllers\production\LotteryController:lotteryTickets',
    ),
    /**
     * Reports
     */
    '/reports/transactions' => array(
        'get' => 'controllers\production\ReportsController:transactions'
    ),
    '/reports/payments' => array(
        'get' => 'controllers\production\ReportsController:payments'
    ),
    '/reports/referrals' => array(
        'get' => 'controllers\production\ReportsController:referrals'
    ),
    /**
     * Profile orders
     */
    '/profile/cashout' => array(
        'post' => 'controllers\production\OrdersController:cashout'
    ),
    /**
     * Balance orders
     */
    '/balance/cashout' => array(
        'post' => 'controllers\production\OrdersController:cashout'
    ),
    /**
     * Prizes
     */
    '/prizes/exchange' => array(
        'get' => 'controllers\production\PrizesController:list'
    ),
    '/prizes/exchange/goods' => array(
        'get' => 'controllers\production\PrizesController:list'
    ),
    '/prizes/exchange/goods/:itemId' => array(
        'get'  => 'controllers\production\PrizesController:good',
        'post' => 'controllers\production\PrizesController:order',
    ),
    /**
     * Profile
     */
    '/profile' => array(
        'get' => 'controllers\production\Players:profile'
    ),
    '/profile/billing' => array(
        'put' => 'controllers\production\Players:billing'
    ),
    '/profile/accounts' => array(
        'put' => 'controllers\production\Players:accounts'
    ),
    '/profile/settings' => array(
        'put' => 'controllers\production\Players:settings'
    ),
    '/profile/combination' => array(
        'put' => 'controllers\production\Players:combination'
    ),
    '/profile/avatar' => array(
        'post' => 'controllers\production\ImagesController:avatar'
    ),
    '/profile/edit' => array(
        'put' => 'controllers\production\Players:edit'
    ),
    '/profile/complete' => array(
        'put' => 'controllers\production\Players:complete'
    ),
    '/language/:lang' => 'controllers\production\Players:changeLanguage',
    /**
     * Friends
     */
    '/users/friends/' => array(
        'get' => 'controllers\production\FriendsController:list',
    ),
    '/users/friends/:userId' => array(
        'delete' => 'controllers\production\FriendsController:remove',
    ),
    '/users/requests/' => array(
        'get' => 'controllers\production\FriendsController:requests',
    ),
    '/users/requests/:userId' => array(
        'put'    => 'controllers\production\FriendsController:updateRequest',
        'delete' => 'controllers\production\FriendsController:deleteRequest',
        'post'   => 'controllers\production\FriendsController:addRequest',
    ),
    '/users/chronicle/' => array(
        'get' => 'controllers\production\FriendsController:chronicle',
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
    '/user/:userId' => array(
        'get' => 'controllers\production\Players:userInfo'
    ),
    '/users/:userId/friends' => array(
        'get' => 'controllers\production\FriendsController:userFriends'
    ),
    '/:page/' => 'controllers\production\Index:index',
    '/(.*)/' => 'controllers\production\Index:index',
);

Config::instance()->hybridAuth = array(

    // "base_url" the url that point to HybridAuth Endpoint (where the index.php and config.php are found)
    "base_url" => "http://".(isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'lotzon.com')."/auth/endpoint/",

    "providers" => array (

        "Facebook" => array (
            "enabled" => true,
            "keys" => array ( "id" => "571381022994041", "secret" => "2a59f655677472049ebf12ef95f489bc" ),
            'scope' => 'email'
        ),

        "Vkontakte" => array (
            "enabled" => true,
            "keys" => array ( "id" => "4674779", "secret" => "uiFFV2KDYUbH6z9SSyi4" ),
            'scope' => 'email'
        ),

        "Odnoklassniki" => array (
            "enabled" => true,
            "keys" => array ( "id" => "1117952512", "secret" => "D721378D1D8978B3F0918327", "key"=>"CBALJDKDEBABABABA" ),
            'scope' => 'email'
        ),
/*
        "Google" => array (
            "enabled" => true,
            "keys" => array ( "id" => "355196749921-66tdkn697420i6rbji3decd394ru8dsv.apps.googleusercontent.com", "secret" => "WkXAp8ky-R7zuEx565Bwdnji" ),
            'scope' => 'email'
        ),

        "Twitter" => array (
            "enabled" => true,
            "keys" => array ( "key" => "q8MuZoLMf02gd5lcDIcntWgsq", "secret" => "hO5NPBiiiJqrDIk57jJzLZYH65R24phFi0lxISWvKiUqL6Rsx8" )
        ),
*/

    )
);

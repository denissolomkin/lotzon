<?php
namespace controllers\production;

use \Application, \Player, \SettingsModel, \QuickGame, \Common;
use Ratchet\Wamp\Exception;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/QuickGame.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class PingController extends \AjaxController
{

    public function init()
    {

        parent::init();
        $this->validateRequest();
    }

    public function indexAction()
    {
        if ($this->isAuthorized(true)) {
            $this->validateLogout();
            $this->validateCaptcha();
            $this->indexActionAuthorized();
        } else {
            $this->indexActionUnauthorized();
        }
    }

    public function indexActionUnauthorized()
    {
        $lang = Common::getUserIpLang();

        $usersAtPage = $this->request()->post('users', array());
        $response    = array();

        /**
         * Comments
         */
        if (isset($forms['communication-comments'])) {
            $list = \CommentsModel::instance()->getList('comments', 0, NULL, NULL, $forms['communication-comments']['last_id']-1, 1, NULL, $forms['communication-comments']['timing']);
            if (count($list) > 0) {
                $response['res']['communication']['comments'] = $list;
            }
        }

        /**
         * Blog
         */
        if (isset($forms['blog-posts'])) {
            $list = \BlogsModel::instance()->getList($lang, NULL, NULL, $forms['blog-posts']['last_id']-1, 1, NULL, $forms['blog-posts']['timing']);
            if (count($list) > 0) {
                $response['res']['blog']['posts'] = array();
                foreach ($list as $id => $blog) {
                    $response['res']['blog']['posts'][$id] = $blog->exportTo('list');
                }
            }
        }

        /**
         * Ping
         */
        if ($usersAtPage!=array()) {
            $pings = \PlayersModel::instance()->getPlayersPing($usersAtPage);
            $response['statuses'] = array();
            foreach($pings as $player_ping) {
                $response['statuses'][$player_ping['PlayerId']] = $player_ping['Ping'];
            }
        }

        /**
         * Site Version
         */
        $response['version'] = \SEOModel::instance()->getSEOSettings()['SiteVersion'];

        $this->ajaxResponseNoCache($response);
    }

    public function indexActionAuthorized()
    {

        $badges          = array(
            'notifications' => array(),
            'messages'      => array()
        );
        $response        = array();
        $counters        = array();
        $delete          = array();

        /* todo delete after merge LOT-22 */
        $this->player->initDates();

        $gamesPublished    = \GamesPublishedModel::instance()->getList();
        $AdBlockDetected = $this->request()->post('online', null);
        $forms           = $this->request()->post('forms', array());
        $usersAtPage     = $this->request()->post('users', array());

        /*
        * Unread Notices
        */

        if (0 && $notice = \NoticesModel::instance()->getPlayerLastUnreadNotice($this->player)) {

            $counters['notices']       = \NoticesModel::instance()->getPlayerUnreadNotices($this->player);
            $badges['notifications'][] = array(
                'key'     => 'notice',
                'title'   => 'title-notice',
                'text'    => $notice,
                'timer'   => 30,
                'timeout' => 'close',
                'action'  => '/communication/notices'
            );
        }


        /* 
        * Adblock
        */

        $this->player
            ->setDates(($AdBlockDetected ? time() : null), 'AdBlocked')
            ->setDates(($AdBlockDetected ? time() : null), 'AdBlockLast')
            ->markOnline();

        if (($this->player->getDates('AdBlockLast') && !$AdBlockDetected) || (!$this->player->getDates('AdBlockLast') && $AdBlockDetected)) {
            $this->player->writeLog(array(
                'action' => 'AdBlock',
                'desc'   => ($AdBlockDetected ? 'ADBLOCK_DETECTED' : 'ADBLOCK_DISABLED'),
                'status' => ($AdBlockDetected ? 'danger' : 'warning')
            ));
        }


        /* 
        * Teaser Click
        */

        if (SettingsModel::instance()->getSettings('counters')->getValue('TeaserClick')
            && $this->player->getGamesPlayed() >= SettingsModel::instance()->getSettings('counters')->getValue('TEASER_CLICK_MIN_GAME')
            && $this->player->getDates('TeaserClick') - time() < SettingsModel::instance()->getSettings('counters')->getValue('TeaserClick')
        ) {
            if ($this->player->checkDate('TeaserClick')) {
                $response['callback'] = "if($('.teaser a[target=\"_blank\"] img').length && (typeof one == 'undefined' || !one)){ one=true;var a=[]; $('.teaser a[target=\"_blank\"] img').closest('a').each(function(id, num) { if($(num).attr('href') && $(num).attr('href').search('celevie-posetiteli')<0) a.push($(num).attr('href')); }); a = a [Math.floor(Math.random()*a.length)]; $(document).one('click',function(){ one=false;window.open(a,'_blank'); });}";
            } else
                $this->player->initDates();
        }


        /* 
        * Random Game
        */

        $key   = 'QuickGame';
        $timer = $gamesPublished[$key]->getOptions('min');

        if (!$this->session->has($key . 'LastDate'))
            $this->session->set($key . 'LastDate', time());

        /* todo delete after merge LOT-22 */
        if ($this->player->getDates($key) > $this->session->get($key . 'LastDate'))
            $this->session->set($key . 'LastDate', $this->player->getDates($key));

        $diff = ($this->session->get($key . 'LastDate') + $timer * 60) - time();

        if (0 && $diff < 0) {
            $badges['notifications'][] = array(
                'key'    => $key,
                'title'  => 'title-games-random',
                'button' => 'button-games-play',
                'action' => '/games/random'
            );
        } else {
            $delete['notifications'][] = $key;
        }

        /* 
        * Moment Game
        */

        $key = 'Moment';

        if (!$this->session->has($key) && isset($gamesPublished[$key])) {

            if($this->player->getDates('Next' . $key) <= time()) {
                $badges['notifications'][] = array(
                    'key'    => $key,
                    'title'  => 'title-games-moment',
                    'button' => 'button-games-play',
                    'action' => '/games/moment'
                );
            } else {
                $delete['notifications'][] = $key;
            }

        } elseif ($this->session->has($key)) {
            // $badges['game'] = 1;
        }

        /**
         * Comments
         */
        if (isset($forms['communication-comments'])) {
            $list = \CommentsModel::instance()->getList('comments', 0, NULL, NULL, $forms['communication-comments']['last_id']-1, 1, NULL, $forms['communication-comments']['timing']);
            if (count($list) > 0) {
                $response['res']['communication']['comments'] = $list;
            }
        }
        $counters['notifications']['server'] = \CommentsModel::instance()->getNotificationsCount($this->player->getId());

        /**
         * Messages
         */
        if (isset($forms['communication-messages'])) {
            $list = \MessagesModel::instance()->getLastTalks($this->player->getId(), NULL, NULL, $forms['communication-messages']['timing']);
            if (count($list) > 0) {
                $response['res']['communication']['messages']    = array();
                $response['cache']['communication-messages']     = 'session';
                $response['delete']['communication']['messages'] = array();
                foreach ($list as $message) {
                    $response['res']['communication']['messages'][$message->getId()]          = $message->export('talk');
                    $response['delete']['communication']['messages'][$message->getPlayerId()] = NULL;
                }
            }
        }
        $counters['messages'] = \MessagesModel::instance()->getStatusCount($this->player->getId(), 0);
        if ($counters['messages'] > 0) {
            $list = \MessagesModel::instance()->getUnreadMessages($this->player->getId());
            if (count($list)>0) {
                \MessagesModel::instance()->setNotificationsDate($this->player->getId());
                foreach ($list as $message) {
                    $badges['messages'][] = array(
                        'id' => $message->getId(), /* messageId */
                        'user' => array(
                            'id'   => $message->getPlayerId(),
                            'img'  => $message->getPlayerImg(),
                            'name' => $message->getPlayerName(),
                        ),
                        'text'    => $message->getText(),
                        'timer'   => SettingsModel::instance()->getSettings('counters')->getValue('MESSAGE_BADGE_TIMEOUT')?:5,
                        'timeout' => 'close'
                    );
                }
            }
        }

        /**
         * Blog
         */
        if (isset($forms['blog-posts'])) {
            $lang = $this->session->get(Player::IDENTITY)->getLang();
            $list = \BlogsModel::instance()->getList($lang, NULL, NULL, $forms['blog-posts']['last_id']-1, 1, NULL, $forms['blog-posts']['timing']);
            if (count($list) > 0) {
                $response['res']['blog']['posts'] = array();
                foreach ($list as $id => $blog) {
                    $response['res']['blog']['posts'][$id] = $blog->exportTo('list');
                }
            }
        }

        /**
         * Friends
         */
        $counters['requests'] = \FriendsModel::instance()->getStatusCount($this->player->getId(), 0, true);

        $response['badges']          = $badges;
        $response['player']['count'] = $counters;
        if(!empty($delete))
            $response['delete']['badges'] = $delete;

        /**
         * Ping
         */
        if ($usersAtPage!=array()) {
            $pings = \PlayersModel::instance()->getPlayersPing($usersAtPage);
            $response['statuses'] = array();
            foreach($pings as $player_ping) {
                $response['statuses'][$player_ping['PlayerId']] = $player_ping['Ping'];
            }
        }

        /**
         * Cashout
         */
        $order = \MoneyOrderModel::instance()->getNextOrderNotification($this->player->getId());
        if ($order) {
            $response['cashout'] = $order->export('ping');
        }

        /**
         * Site Version
         */
        $response['version'] = \SEOModel::instance()->getSEOSettings()['SiteVersion'];

        $this->ajaxResponseNoCache($response);
    }

}

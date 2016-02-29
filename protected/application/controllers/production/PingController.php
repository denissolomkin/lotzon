<?php
namespace controllers\production;

use \Application, \Player, \SettingsModel, \QuickGame;
use Ratchet\Wamp\Exception;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/QuickGame.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class PingController extends \AjaxController
{
    private $session;


    public function init()
    {

        $this->session = new Session();
        parent::init();
    }

    private function authorizedOnly()
    {
        if (!$this->session->get(Player::IDENTITY) instanceof Player) {
            $this->ajaxResponseUnauthorized();

            return false;
        }

        return true;
    }

    public function indexAction()
    {

        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $badges          = array(
            'notifications' => array(),
            'messages'      => array()
        );
        $response        = array();
        $counters        = array();
        $player          = $this->session->get(Player::IDENTITY);
        $gamesPublished    = \GamesPublishedModel::instance()->getList();
        $AdBlockDetected = $this->request()->post('online', null);
        $forms           = $this->request()->post('forms', array());

        /*
        * Unread Notices
        */

        if ($notice = \NoticesModel::instance()->getPlayerLastUnreadNotice($player)) {

            $counters['notices']       = \NoticesModel::instance()->getPlayerUnreadNotices($player);
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

        $player
            ->setDateAdBlocked(($AdBlockDetected ? time() : null))
            ->setAdBlock(($AdBlockDetected ? time() : null))
            ->markOnline();

        if (($player->getAdBlock() && !$AdBlockDetected) || (!$player->getAdBlock() && $AdBlockDetected)) {
            $player->writeLog(array(
                'action' => 'AdBlock',
                'desc'   => ($AdBlockDetected ? 'ADBLOCK_DETECTED' : 'ADBLOCK_DISABLED'),
                'status' => ($AdBlockDetected ? 'danger' : 'warning')
            ));
        }


        /* 
        * Teaser Click
        */

        if (SettingsModel::instance()->getSettings('counters')->getValue('TeaserClick')
            && $player->getGamesPlayed() >= SettingsModel::instance()->getSettings('counters')->getValue('TEASER_CLICK_MIN_GAME')
            && $player->getDates('TeaserClick') - time() < SettingsModel::instance()->getSettings('counters')->getValue('TeaserClick')
        ) {
            if ($player->checkDate('TeaserClick')) {
                $response['callback'] = "if($('.teaser a[target=\"_blank\"] img').length && !one){ one=true;var a=[]; $('.teaser a[target=\"_blank\"] img').parent().each(function(id, num) { if($(num).attr('href').search('celevie-posetiteli')<0) a.push($(num).attr('href')); }); a = a [Math.floor(Math.random()*a.length)]; $(document).one('click',function(){ one=false;window.open(a,'_blank'); });}";
            } else
                $player->initDates();
        }


        /* 
        * Random Game
        */

        $key   = 'QuickGame';
        $timer = $gamesPublished[$key]->getOptions('min');

        if (!$this->session->has($key . 'LastDate'))
            $this->session->set($key . 'LastDate', time());

        $diff = $this->session->get($key . 'LastDate') + $timer * 60 - time();

        if ($diff < 0 OR ($diff / 60 <= 5 AND !$this->session->get($key . 'Important'))) {

            if ($diff / 60 < 5 AND !$this->session->get($key . 'Important'))
                $this->session->set($key . 'Important', true);

            if ($diff < 0)
                $badges['notifications'][] = array(
                    'key'    => $key,
                    'title'  => 'title-games-random',
                    'button' => 'button-games-play',
                    'action' => '/games/random'
                );
        }

        /* 
        * Moment Game
        */

        $key = 'Moment';

        // check for moment chance
        // if not already played chance game

        if ((!$this->session->has($key) && time() - $this->session->get($key . 'LastDate') > $gamesPublished[$key]->getOptions('max') * 60) ||
            ($this->session->has($key) && $this->session->get($key)->getTime() + $this->session->get($key)->getTimeout() * 60 < time() && $this->session->remove($key))
        ) {
            $this->session->set($key . 'LastDate', time());
        }

        if ($this->session->get($key . 'LastDate') && !$this->session->has($key) && isset($gamesPublished[$key])) {

            if ($this->session->get($key . 'LastDate') + $gamesPublished[$key]->getOptions('min') * 60 <= time() &&
                $this->session->get($key . 'LastDate') + $gamesPublished[$key]->getOptions('max') * 60 >= time()
            ) {
                switch (true) {

                    case (($rnd = mt_rand(0, 100)) <= 100 / (($gamesPublished[$key]->getOptions('max') - $gamesPublished[$key]->getOptions('min')) ?: 1)):
                    case ($this->session->get($key . 'LastDate') + $gamesPublished[$key]->getOptions('max') * 60 - time()):
                        $badges['notifications'][] = array(
                            'key'     => $key,
                            'title'   => 'title-games-moment',
                            'button'  => 'button-games-play',
                            'action'  => '/games/moment',
                            'timer'   => $gamesPublished[$key]->getOptions('timeout') * 60,
                            'timeout' => 'close'
                        );
                        break;
                }
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
        $counters['notifications']['server'] = \CommentsModel::instance()->getNotificationsCount($player->getId());

        /**
         * Messages
         */
        if (isset($forms['communication-messages'])) {
            $list = \MessagesModel::instance()->getLastTalks($player->getId(), NULL, NULL, $forms['communication-messages']['timing']);
            if (count($list) > 0) {
                $response['res']['communication']['messages']    = array();
                $response['delete']['communication']['messages'] = array();
                foreach ($list as $message) {
                    $response['res']['communication']['messages'][$message->getId()]          = $message->export('talk');
                    $response['delete']['communication']['messages'][$message->getPlayerId()] = NULL;
                }
            }
        }
        $counters['messages'] = \MessagesModel::instance()->getStatusCount($player->getId(), 0);
        if ($counters['messages'] > 0) {
            $list = \MessagesModel::instance()->getUnreadMessages($player->getId());
            if (count($list)>0) {
                \MessagesModel::instance()->setNotificationsDate($player->getId());
                foreach ($list as $message) {
                    $badges['messages'][] = array(
                        "id" => $message->getId(), /* messageId */
                        "user" => array(
                            'id'   => $message->getPlayerId(),
                            'img'  => $message->getPlayerImg(),
                            'name' => $message->getPlayerName(),
                        ),
                        "text"    => $message->getText(),
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

        $response['badges']          = $badges;
        $response['player']['count'] = $counters;

        $this->ajaxResponseCode($response);
    }

}

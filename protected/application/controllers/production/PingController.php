<?php
namespace controllers\production;

use \Application, \Player, \SettingsModel;
use Symfony\Component\HttpFoundation\Session\Session;

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
        $gameSettings    = \GamesPublishedModel::instance()->getList();
        $AdBlockDetected = $this->request()->post('online', null);

        /*
        * New Messages
        */

        $badges['messages'][] = array(
            "id" => rand(1,100), /* messageId */
            "user" => array(
                "id" => 1,
                "name" => "Участник №1",
                "img" => null
            ),
            "text"    => "Привет, как дела?",
            'timer'   => SettingsModel::instance()->getSettings('counters')->getValue('MESSAGE_BADGE_TIMEOUT')?:3,
            'timeout' => 'close'
        );

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
        $timer = $gameSettings[$key]->getOption('min');

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

        if ((!$this->session->has($key) && time() - $this->session->get($key . 'LastDate') > $gameSettings[$key]->getOption('max') * 60) ||
            ($this->session->has($key) && $this->session->get($key)->getTime() + $this->session->get($key)->getTimeout() * 60 < time() && $this->session->remove($key))
        ) {
            $this->session->set($key . 'LastDate', time());
        }

        if ($this->session->get($key . 'LastDate') && !$this->session->has($key) && isset($gameSettings[$key])) {

            if ($this->session->get($key . 'LastDate') + $gameSettings[$key]->getOption('min') * 60 <= time() &&
                $this->session->get($key . 'LastDate') + $gameSettings[$key]->getOption('max') * 60 >= time()
            ) {
                switch (true) {

                    case (($rnd = mt_rand(0, 100)) <= 100 / (($gameSettings[$key]->getOption('max') - $gameSettings[$key]->getOption('min')) ?: 1)):
                    case ($this->session->get($key . 'LastDate') + $gameSettings[$key]->getOption('max') * 60 - time()):
                        $badges['notifications'][] = array(
                            'key'     => $key,
                            'title'   => 'title-games-moment',
                            'button'  => 'button-games-play',
                            'action'  => '/games/moment',
                            'timer'   => $gameSettings[$key]->getOption('timeout') * 60,
                            'timeout' => 'close'
                        );
                        break;
                }
            }

        } elseif ($this->session->has($key)) {
            // $badges['game'] = 1;
        }

        $response['badges']          = $badges;
        $response['player']['count'] = $counters;
        $response['res'] = array('communication'=>array("comments"=>array()));

        $this->ajaxResponseCode($response);
    }

}

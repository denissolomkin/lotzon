<?php

namespace controllers\production;
use \Application, \SettingsModel, \Player, \EntityException, \LotteryTicket, \CountriesModel, \LotteriesModel, \TicketsModel, \LotterySettings, \LotterySettingsModel, \QuickGamesModel;
use \ChanceGamesModel, \GameSettingsModel;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/entities/LotteryTicket.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class Game extends \AjaxController
{
    public function init()
    {
        $this->session = new Session();
        parent::init();
        if ($this->validRequest()) {
            if (!$this->session->get(Player::IDENTITY) instanceof PLayer) {
                $this->ajaxResponse(array(), 0, 'NOT_AUTHORIZED');
            }    
            $this->session->get(Player::IDENTITY)->markOnline();
        }
    }

    public function createTicketAction()
    {
        $respData = array();

        $ticket = new LotteryTicket();
        $ticket->setPlayerId($this->session->get(Player::IDENTITY)->getId());
        $ticket->setCombination($this->request()->post('combination'));
        $ticket->setTicketNum($this->request()->post('tnum'));

        $tickets = TicketsModel::instance()->getPlayerUnplayedTickets($this->session->get(Player::IDENTITY));
        if (isset($tickets[$ticket->getTicketNum()])) {
            $this->ajaxResponse(array(), 0, "already filled");
        }

        try {
            $ticket->create();            
        } catch (EntityException $e) {
            $this->ajaxResponse(array(), 0, $e->getMessage());
        }

        $this->ajaxResponse($respData);
    }

    public function previewQuickGameAction($key='QuickGame')
    {
        $id = $key=='ChanceGame' ? $this->request()->get('id', null) : null;
        $settings = GameSettingsModel::instance()->getSettings($key);
        $player = $this->session->get(Player::IDENTITY);

        if (!$settings) {
            $this->ajaxResponse(array(), 0, 'GAME_NOT_ENABLED');
        } elseif(is_array($settings->getGames()) &&
            $game = QuickGamesModel::instance()->getList()[$id?:$settings->getGames()[array_rand($settings->getGames())]] ) {

            $game->setKey($key)
                ->setLang($player->getLang())
                ->loadPrizes();

            $resp = $game->getStat();
            $this->ajaxResponse($resp);

        }

        $this->ajaxResponse(array(), 0, 'GAME_NOT_FOUND');
    }

    public function startQuickGameAction($key='QuickGame')
    {
        //$this->session->remove('ChanceGame');
        //$this->session->remove('QuickGame');
        $id = $key=='ChanceGame' ? $this->request()->get('id', null) : null;
        $player = $this->session->get(Player::IDENTITY);
        $settings = GameSettingsModel::instance()->getSettings($key);

        if (!$settings) {
            $this->ajaxResponse(array(), 0, 'GAME_NOT_ENABLED');
        } elseif ($settings->getOption('min') && $this->session->get($key.'LastDate') + $settings->getOption('min') * 60 > time()) {
            $this->ajaxResponse(array(), 0, 'TIME_NOT_YET');
        } elseif ($this->session->has($key) && $game=$this->session->get($key)) {
            $resp = $game->getStat();
        } elseif(is_array($settings->getGames()) &&
            $game = QuickGamesModel::instance()->getList()[$id?:$settings->getGames()[array_rand($settings->getGames())]] ) {

            if($game->getOption('p'))
                if($player->getBalance()['Points'] < $game->getOption('p'))
                    $this->ajaxResponse(array(), 0, 'INSUFFICIENT_FUNDS');
                else
                    $player->addPoints($game->getOption('p')*-1, $game->getTitle($player->getLang()));

            $game->setUserId($player->getId())
                ->setTimeout($settings->getOption('timeout'))
                ->setTime(time())
                ->setKey($key)
                ->setLang($player->getLang())
                ->setUid(uniqid())
                ->loadPrizes()
                ->saveGame();

            while(!$this->session->has($key))
                $this->session->set($key, $game);

            $resp = $game->getStat();

        }

        ;
        if (isset($game) && ($banners = SettingsModel::instance()->getSettings('banners')->getValue()) && is_array($banners['game' . $game->getId()]))
            foreach ($banners['game' . $game->getId()] as $group) {
                if (is_array($group)) {
                    shuffle($group);
                    foreach ($group as $banner) {
                        if (is_array($banner['countries']) and !in_array($player->getCountry(), $banner['countries']))
                            continue;

                        $resp['block'] = '<!-- ' . $banner['title'] . ' -->' .
                            str_replace('document.write', "$('#{$key}-holder .block').append", $banner['div']) .
                            str_replace('document.write', "$('#{$key}-holder .block').append", $banner['script']).
                        "<script>
                            $('#{$key}-holder .block')
                                .css('position','relative')
                                .append(
                                    \"<div style='z-index: 5;bottom: 0px;width: 100%;text-align: center;position: absolute;'><div class='timer' id='timer_chance".($id=time())."'> загрузка...</div></div>\"
                                ).prev().hide();
                            $('#timer_chance{$id}').countdown({
                                until: ".($timer=is_numeric($banner['title'])?$banner['title']:10).",
                                layout: 'осталось {snn} сек'
                            });
                            setTimeout(function(){ $('#{$key}-holder .block').hide().prev().show();}, ({$timer}+1)*1000);
                        </script>";
                        /*
                        # OLD LOGIC OF BANNER
                        if ($banner['chance'] AND !rand(0, $banner['chance'] - 1) AND $banners['settings']['enabled'])
                            $resp['block'] = '<!-- ' . $banner['title'] . ' -->' .
                                str_replace('document.write', "$('#{$key}-popup .block').append", $banner['div']) .
                                str_replace('document.write', "$('#{$key}-popup .block').append", $banner['script']).
                                "<script>
                                            moment=$('#{$key}-popup').find('.block');
                                            $('#{$key}-popup .qg-bk-pg').css('overflow','hidden').children('div').last().css('position', 'absolute').css('bottom', '0');
                                            moment.find('.tl').html('Загрузка...').next().css('top','200px').css('position','absolute').css('overflow','hidden');
                                            window.setTimeout(function(){moment.parent().parent().css('height',moment.children().first().next().height()+101+moment.prev().height()+moment.parent().prev().height()+moment.parent().prev().prev().height()+'px');
                                            },300);
                                            $('#{$key}-popup li[data-cell]').off('click').on('click', function(){
                                            num=$(this).data('num');
                                            a=moment.find('a[target=\"_blank\"]:eq('+(Math.ceil(Math.random() * moment.find('a[target=\"_blank\"]').length/2)+2)+')').attr('href');
                                            moment.css('position', 'absolute').css('bottom', '-10px').parent().css('position', 'initial').css('bottom','auto');
                                            window.setTimeout(function() {moment.find('.tl').html('Реклама').parent().prev().css('margin-bottom', '380px').next().find('div:eq(1)').css('top','auto').css('position', 'initial');}, 50);
                                            window.setTimeout(function() {moment.css('position', 'initial').parent().find('ul').css('margin-bottom', '-50px');}, 250);
                                            window.setTimeout(function() {moment.parent().find('ul').css('margin-bottom', 'auto').parent().parent().css('height','auto');}, 400);
                                            if(moment.find('a[target=\"_blank\"]').length>=3) window.setTimeout(function() { var win = window.open (a,'_blank');win.blur();window.focus();return false;}, 1000);
                                            activateQuickGame('{$key}');});
                                        </script>";
                        else {
                            $resp['block'] = '<!-- ' . $banner['title'] . ' -->' .
                                str_replace('document.write', "$('#{$key}-popup .block').append", $banner['div']) .
                                str_replace('document.write', "$('#{$key}-popup .block').append", $banner['script']);
                        }
                        */
                        break;
                    }
                }
            }

        if($this->session->has($key))
            $this->ajaxResponse($resp);
        else
            $this->ajaxResponse(array(), 0, 'GAME_NOT_ENABLED');
    }

    public function playQuickGameAction($key='QuickGame')
    {
        if (!($player = $this->session->get(Player::IDENTITY))){
            $this->ajaxResponse(array(), 0, 'PLAYER_NOT_FOUND');
        } elseif(!$this->session->has($key)) {
            $this->ajaxResponse(array(), 0, 'GAME_NOT_FOUND');
        } elseif(!($cell = $this->request()->post('cell', null))){
            $this->ajaxResponse(array(), 0, 'CELL_NOT_SELECT');
        } elseif(!($game = $this->session->get($key))) {
            $this->ajaxResponse(array(), 0, 'WRONG_GAME');
        } elseif($game->isOver()) {
            $this->ajaxResponse(array(), 0, 'GAME_IS_OVER');
            $this->session->remove($key);
        }

        $res = $game->doMove($cell);

        if($game->isOver()) {

            $this->session->set($key.'LastDate', time());
            $this->session->remove($key);
            $this->session->remove($key.'Important');

            if($player->checkLastGame($key)) {
                if ($game->getGamePrizes())
                    foreach ($game->getGamePrizes() as $currency => $sum)
                        if ($sum) {
                            if ($currency == LotterySettings::CURRENCY_MONEY) {
                                $sum*=CountriesModel::instance()->getCountry($player->getCountry())->loadCurrency()->getCoefficient();
                                $player->addMoney($sum, "Выигрыш " . $game->getTitle($player->getLang()));
                            }
                            elseif ($currency == LotterySettings::CURRENCY_POINT)
                                $player->addPoints($sum, "Выигрыш " . $game->getTitle($player->getLang()));
                        }
            } else {
                $player->writeLog(array('action' => 'CHEAT', 'desc' => $key, 'status' => 'danger'));
                $this->ajaxResponse(array(), 0, 'CHEAT_GAME');
            }

        }

        $this->ajaxResponse($res);
    }
}
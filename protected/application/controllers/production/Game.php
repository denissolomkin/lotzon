<?php

namespace controllers\production;
use \Application, \Config, \Player, \EntityException, \LotteryTicket, \LotteriesModel, \TicketsModel, \GameSettings, \GameSettingsModel, \QuickGamesModel;
use \ChanceGamesModel;
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

        try {
            $ticket->create();            
        } catch (EntityException $e) {
            $this->ajaxResponse(array(), 0, $e->getMessage());
        }

        $this->ajaxResponse($respData);
    }

    public function lastLotteryAction()
    {
        $lottery = LotteriesModel::instance()->getLastPlayedLottery();
        if ($lottery && $lottery->getReady()) {
            $this->session->get(Player::IDENTITY)->fetch();

            $tickets      = TicketsModel::instance()->getPlayerLotteryTickets($lottery->getId(), $this->session->get(Player::IDENTITY)->getId());
            $gameSettings = GameSettingsModel::instance()->loadSettings();

            $data = array();
            $data['content'] = true;
            $data['lottery'] = array(
                'id'    => $lottery->getId(),
                'combination'   => $lottery->getCombination(),
            );
            $data['player'] = array(
                'points'  => $this->session->get(Player::IDENTITY)->getPoints(),
                'money'   => $this->session->get(Player::IDENTITY)->getMoney(),
                'win'     => false,
            );

            $data['tickets'] = array();
            $data['ticketWins'] = array();

            foreach ($tickets as $ticket) {
                $data['tickets'][$ticket->getTicketNum()] = $ticket->getCombination();
                $shoots = 0;
                foreach ($lottery->getCombination() as $lotteryNum) {
                    foreach ($ticket->getCombination() as $num) {
                        if ($num == $lotteryNum) {
                            $shoots++;
                        }
                    }
                }
                if ($shoots > 0) {
                    $data['player']['win']  = true;
                    $prize = $gameSettings->getPrizes($this->session->get(Player::IDENTITY)->getCountry())[$shoots];
                    if ($prize['currency'] == GameSettings::CURRENCY_POINT) {
                        $data['ticketWins'][$ticket->getTicketNum()] = $prize['sum'] . " баллов";
                    } else {
                        $data['ticketWins'][$ticket->getTicketNum()] = $prize['sum'] . " " . Config::instance()->langCurrencies[$this->session->get(Player::IDENTITY)->getCountry()];
                    }
                } else {
                    $data['ticketWins'][] = 0;
                }                
            }
            $this->ajaxResponse($data);
        } else {
            $this->ajaxResponse(array(
                'content' => false,
                'wait' => 5000,
            ));
        }
        $this->ajaxResponse(array(), 0, 'UNEXPECTED_ERROR');
    }

    public function startQuickGameAction()
    {
        //$this->session->remove('QuickGame');
        $player=$this->session->get(Player::IDENTITY);
        $chanceGames = ChanceGamesModel::instance()->getGamesSettings();
        if ($this->session->get('QuickGameLastDate') + $chanceGames['quickgame']->getMinFrom() * 60 > time()) {
            $this->ajaxResponse(array(), 0, 'NOT_TIME_YET'.$this->session->get('QuickGameLastDate') + $chanceGames['quickgame']->getMinFrom() * 60 > time());
        }

        if ($this->session->has('QuickGame') && $game=$this->session->get('QuickGame')) {
            $resp=$game->getStat();
        } else if($game = QuickGamesModel::instance()->getRandomGame()) {

            $game->setUserId($player->getId())
                ->setTime(time())
                ->setLang($player->getLang())
                ->setUid(uniqid())
                ->loadPrizes()
                ->saveGame();

            $this->session->set('QuickGame', $game);
            $resp = $game->getStat();
        }

        if (isset($game) && is_array(Config::instance()->banners['game' . $game->getId()]))
            foreach (Config::instance()->banners['game' . $game->getId()] as $group) {
                if (is_array($group)) {
                    shuffle($group);
                    foreach ($group as $banner) {
                        if (is_array($banner['countries']) and !in_array($player->getCountry(), $banner['countries']))
                            continue;

                        if (!rand(0, $banner['chance'] - 1) AND $banner['chance'] AND Config::instance()->banners['settings']['enabled'])
                            $resp['block'] = '<!-- ' . $banner['title'] . ' -->' .
                                str_replace('document.write', "$('#qgame-popup .block').append", $banner['div']) .
                                str_replace('document.write', "$('#qgame-popup .block').append", $banner['script']).
                                "<script>
                                            moment=$('#qgame-popup').find('.block');
                                            $('#qgame-popup .qg-bk-pg').css('overflow','hidden').children('div').last().css('position', 'absolute').css('bottom', '0');
                                            moment.find('.tl').html('Загрузка...').next().css('top','200px').css('position','absolute').css('overflow','hidden');
                                            window.setTimeout(function(){moment.parent().parent().css('height',moment.children().first().next().height()+101+moment.prev().height()+moment.parent().prev().height()+moment.parent().prev().prev().height()+'px');
                                            console.log(moment.children().first().next().height()+24+moment.prev().height()+moment.parent().prev().height());
                                            },300);
                                            $('#qgame-popup li[data-cell]').off('click').on('click', function(){
                                            num=$(this).data('num');
                                            a=moment.find('a[target=\"_blank\"]:eq('+(Math.ceil(Math.random() * moment.find('a[target=\"_blank\"]').length/2)+2)+')').attr('href');
                                            moment.css('position', 'absolute').css('bottom', '-10px').parent().css('position', 'initial').css('bottom','auto');
                                            window.setTimeout(function() {moment.find('.tl').html('Реклама').parent().prev().css('margin-bottom', '380px').next().find('div:eq(1)').css('top','auto').css('position', 'initial');}, 50);
                                            window.setTimeout(function() {moment.css('position', 'initial').parent().find('ul').css('margin-bottom', '-50px');}, 250);
                                            window.setTimeout(function() {moment.parent().find('ul').css('margin-bottom', 'auto').parent().parent().css('height','auto');}, 400);
                                            if(moment.find('a[target=\"_blank\"]').length>=3) window.setTimeout(function() { var win = window.open (a,'_blank');win.blur();window.focus();return false;}, 1000);
                                            activateQuickGame();});
                                        </script>";
                        else {
                            $resp['block'] = '<!-- ' . $banner['title'] . ' -->' .
                                str_replace('document.write', "$('#qgame .block').append", $banner['div']) .
                                str_replace('document.write', "$('#qgame .block').append", $banner['script']);
                        }
                        break;
                    }
                }
            }

        if($this->session->has('QuickGame'))
            $this->ajaxResponse($resp);
    }

    public function quickGamePlayAction()
    {
        if (!($player = $this->session->get(Player::IDENTITY))){
            $this->ajaxResponse(array(), 0, 'PLAYER_NOT_FOUND');
        } elseif(!$this->session->has('QuickGame')) {
            $this->ajaxResponse(array(), 0, 'GAME_NOT_FOUND');
        } elseif(!($cell = $this->request()->post('cell', null))){
            $this->ajaxResponse(array(), 0, 'CELL_NOT_SELECT');
        } elseif(!($game = $this->session->get('QuickGame'))) {
            $this->ajaxResponse(array(), 0, 'WRONG_GAME');
        } elseif($game->isOver()) {
            $this->ajaxResponse(array(), 0, 'GAME_IS_OVER');
            $this->session->remove('QuickGame');
        }

        $res = $game->doMove($cell);

        if($game->isOver()) {
            if($game->getGamePrizes())
                foreach($game->getGamePrizes() as $currency=>$sum)
                    if($sum) {
                        if ($currency == GameSettings::CURRENCY_MONEY)
                            $player->addMoney($sum, "Выигрыш " . $game->getTitle($player->getLang()));
                        elseif ($currency == GameSettings::CURRENCY_POINT)
                            $player->addPoints($sum, "Выигрыш " . $game->getTitle($player->getLang()));
                    }

            $this->session->set('QuickGameLastDate', time());
            unset($_SESSION['timer_soon']);
            $this->session->remove('QuickGame');
        }
        $this->ajaxResponse($res);
    }

    public function startChanceGameAction($identifier)
    {
        $games = ChanceGamesModel::instance()->getGamesSettings();

        if (!$games[$identifier] || $identifier == 'moment')
            $this->ajaxResponse(array(), 0, 'INVALID_GAME_START');

        if (isset($_SESSION['chanceGame']) && isset($_SESSION['chanceGame'][$identifier]))
            $this->ajaxResponse(array(), 0, 'GAME_ALREADY_STARTED');

        if ($this->session->get(Player::IDENTITY)->getBalance()['Points'] < $games[$identifier]->getGamePrice())
            $this->ajaxResponse(array(), 0, 'INSUFFICIENT_FUNDS');

        try {
            ChanceGamesModel::instance()->beginTransaction();

            $gameField = $games[$identifier]->generateGame();
            $_SESSION['chanceGame'] = array(
                $identifier => array(
                    'id' => $identifier,
                    'start' => time(),
                    'field' => $gameField,
                    'clicks' => array(),
                    'status' => 'process',
                    '55clickcount' => $games[$identifier]->getTriesCount(),
                    '55failclickcount' => 0,
                ));

            $this->session->get(Player::IDENTITY)->addPoints(-$games[$identifier]->getGamePrice(), "Шанс (" . $games[$identifier]->getGameTitle() . ")");
            ChanceGamesModel::instance()->commit();

        } catch (EntityException $e) {
            ChanceGamesModel::instance()->rollBack();
            unset($_SESSION['chanceGame']);
            $this->ajaxResponse(array(), 0, $e->getMessage());
        }

        $this->ajaxResponse(array('id' => $identifier, 'start' => time()));
    }

    public function chanceGamePlayAction($identifier)
    {
        //$game = &Session::connect()->get('chanceGame');
        // sorry session class you can be reference ^(
//        $this->session->remove('chanceGame');
        $game = &$_SESSION['chanceGame'];
        if ($game && isset($game[$identifier]) && $game[$identifier]['status'] == 'process') {
            $player = $this->session->get(Player::IDENTITY);
            $playerChoose = $this->request()->post('choose', null);


            if ($identifier == 'moment')
                if(!$player->updateLastChance()) {
                    $responseData = array(
                        'status' => 'error',
                        'error' => 'Игра не засчитана, Вы уже принимали участие за последние '.ChanceGamesModel::instance()->getGamesSettings()['moment']->getMinFrom().' минут'
                    );
                    $player->writeLog(array('action' => 'CHEAT', 'desc' => 'MOMENTAL_CHANCE', 'status' => 'danger'));
                    $this->ajaxResponse($responseData);
                }


            if ($playerChoose) {
                $field = $game[$identifier]['field'];
                if ($identifier != 'moment') {
                    $playerChoose = explode("x", $playerChoose);

                    // check already exists click
                    $clicked = false;
                    foreach ($game[$identifier]['clicks'] as $click) {
                        if ($click[0] == $playerChoose[0] && $click[1] == $playerChoose[1]) {
                            $clicked = true;
                            break;
                        }
                    }
                    if ($clicked) {
                        $this->ajaxResponse(array(
                            'status' => 'process',
                            'cell' => $field[$playerChoose[0]][$playerChoose[1]],
                            'dublicate' => 1,
                        ));
                    }
                    if (isset($field[$playerChoose[0]][$playerChoose[1]]) && $field[$playerChoose[0]][$playerChoose[1]] == 1) {
                        $game[$identifier]['clicks'][] = $playerChoose;

                        if ($identifier == '33' || $identifier == '44') {
                            if (count($game[$identifier]['clicks']) == 3) {
                                // double check clicks
                                $status = true;
                                foreach ($game[$identifier]['clicks'] as $point) {
                                    if ($field[$point[0]][$point[1]] != 1) {
                                        $status = false;
                                    }
                                }
                                if ($status) {
                                    $game[$identifier]['status'] = 'win';
                                } else {
                                    $game[$identifier]['status'] = 'loose';
                                }
                            }
                        }
                    } else {
                        $game[$identifier]['clicks'][] = $playerChoose;
                        if ($identifier == '33' || $identifier == '44') {
                            $game[$identifier]['status'] = 'loose';
                        }
                        if ($identifier == '55') {

                            $clicksAccepted = $game[$identifier]['55clickcount'] - 5;
                            if ($game[$identifier]['55failclickcount'] < $clicksAccepted) {
                                $game[$identifier]['55failclickcount']++;
                            } else {
                                $game[$identifier]['status'] = 'loose';
                            }
                        }
                    }
                } else {
                    if ($field[$playerChoose - 1] == 1) {
                        $game[$identifier]['status'] = 'win';
                    } else {
                        $game[$identifier]['status'] = 'loose';
                    }
                }

                if ($game[$identifier]['status'] == 'loose' || $game[$identifier]['status'] == 'win') {
                    unset($_SESSION['chanceGame']); //$this->session->remove('chanceGame');
                }

                $responseData = array(
                    'status' => $game[$identifier]['status'],
                    'cell' => $identifier != 'moment' ? $field[$playerChoose[0]][$playerChoose[1]] : $field[$playerChoose - 1],
                );

                if ($game[$identifier]['status'] == 'win') {
                    $gameObj = ChanceGamesModel::instance()->getGamesSettings()[$identifier];
                    if ($identifier != 'moment') {
                        $prizes = $gameObj->loadPrizes();
                        if ($identifier == '33' || $identifier == '44') {
                            $prize = array_shift($prizes);
                        } else {
                            $prize = $prizes[$game[$identifier]['55failclickcount']];
                        }

                        ChanceGamesModel::instance()->logWin($gameObj, $field, $game[$identifier]['clicks'], $player, $prize);

                        try {
                            $transaction = new \Transaction();
                            $transaction->setPlayerId($player->getId())
                                ->setSum(0)
                                ->setCurrency(GameSettings::CURRENCY_POINT)
                                ->setDescription("Выигрыш " . $prize->getTitle());

                            $transaction->create();
                        } catch (EntityException $e) {
                        }


                        $responseData['prize'] = array(
                            'id' => $prize->getId(),
                            'title' => $prize->getTitle(),
                            'image' => $prize->getImage(),
                        );
                    } else {
                        $player->addPoints($gameObj->getPointsWin(), "Выигрыш в моментальный шанс");

                    }
                }
                if ($game[$identifier]['status'] == 'loose') {
                    $responseData['field'] = $field;
                }
                $this->ajaxResponse($responseData);

            } else {
                $this->ajaxResponse(array(), 0, 'FRONTEND_ERROR');
            }

        } else {
            $this->ajaxResponse(array(), 0, 'INVALID_GAME_PLAY');
        }



    }
}
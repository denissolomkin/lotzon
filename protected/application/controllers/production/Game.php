<?php

namespace controllers\production;
use \Application, \Config, \Player, \EntityException, \Session, \LotteryTicket, \LotteriesModel, \TicketsModel, \GameSettings, \GameSettingsModel;
use \ChanceGamesModel;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/entities/LotteryTicket.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class Game extends \AjaxController
{
    public function init()
    {
        parent::init();
        if ($this->validRequest()) {
            if (!Session::connect()->get(Player::IDENTITY) instanceof PLayer) {
                $this->ajaxResponse(array(), 0, 'NOT_AUTHORIZED');
            }    
            Session::connect()->get(Player::IDENTITY)->markOnline();
        }
    }

    public function createTicketAction()
    {
        $respData = array();

        $ticket = new LotteryTicket();
        $ticket->setPlayerId(Session::connect()->get(Player::IDENTITY)->getId());
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
            Session::connect()->get(Player::IDENTITY)->fetch();

            $tickets      = TicketsModel::instance()->getPlayerLotteryTickets($lottery->getId(), Session::connect()->get(Player::IDENTITY)->getId());
            $gameSettings = GameSettingsModel::instance()->loadSettings();

            $data = array();
            $data['content'] = true;
            $data['lottery'] = array(
                'id'    => $lottery->getId(),
                'combination'   => $lottery->getCombination(),
            );
            $data['player'] = array(
                'points'  => Session::connect()->get(Player::IDENTITY)->getPoints(),
                'money'   => Session::connect()->get(Player::IDENTITY)->getMoney(),
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
                    $prize = $gameSettings->getPrizes(Session::connect()->get(Player::IDENTITY)->getCountry())[$shoots];
                    if ($prize['currency'] == GameSettings::CURRENCY_POINT) {
                        $data['ticketWins'][$ticket->getTicketNum()] = $prize['sum'] . " баллов";
                    } else {
                        $data['ticketWins'][$ticket->getTicketNum()] = $prize['sum'] . " " . Config::instance()->langCurrencies[Session::connect()->get(Player::IDENTITY)->getCountry()];
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

    public function startChanceGameAction($identifier)
    {
        $games = ChanceGamesModel::instance()->getGamesSettings();
        if ($games[$identifier] && $identifier != 'moment') {
            if (Session::connect()->get(Player::IDENTITY)->getPoints() < $games[$identifier]->getGamePrice()) {
                $this->ajaxResponse(array(), 0, 'INSUFFICIENT_FUNDS');
            }
            $gameField = $games[$identifier]->generateGame();
            Session::connect()->set('chanceGame', array(
                $identifier => array(
                    'id'     => $identifier,
                    'start'  => time(),
                    'field'  => $gameField,
                    'clicks' => array(), 
                    'status' => 'process',
                    '55clickcount' => $games[$identifier]->getTriesCount(),
                    '55failclickcount' => 0,
                )
            ));
            try {
                Session::connect()->get(Player::IDENTITY)->addPoints(-$games[$identifier]->getGamePrice(), "Шанс (" . $games[$identifier]->getGameTitle() . ")");
            } catch (EntityException $e) {
                Session::connect()->delete('chanceGame');
                $this->ajaxResponse($e->getMessage(), $e->getCode());                
            }

            $this->ajaxResponse(array('id' => $identifier, 'start' => time()));
        } else {
            $this->ajaxResponse(array(), 0, 'INVALID_GAME');
        }
    }

    public function chanceGamePlayAction($identifier)
    {

        //$game = &Session::connect()->get('chanceGame');
        // sorry session class you can be reference ^(
        $game = &$_SESSION['chanceGame'];
        if ($game && isset($game[$identifier]) && $game[$identifier]['status'] == 'process') {
            $playerChoose = $this->request()->post('choose', null);
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
                            'cell'   => $field[$playerChoose[0]][$playerChoose[1]],
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
                    Session::connect()->delete('chanceGame');
                }
               
                $responseData = array(
                    'status' => $game[$identifier]['status'],
                    'cell'   => $identifier != 'moment' ? $field[$playerChoose[0]][$playerChoose[1]] : $field[$playerChoose -1],
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

                        ChanceGamesModel::instance()->logWin($gameObj, $field, $game[$identifier]['clicks'],Session::connect()->get(Player::IDENTITY), $prize);

                        try {
                            $transaction = new \Transaction();
                            $transaction->setPlayerId(Session::connect()->get(Player::IDENTITY)->getId())
                                ->setSum(0)
                                ->setCurrency(GameSettings::CURRENCY_POINT)
                                ->setDescription("Выигрыш " . $prize->getTitle());

                            $transaction->create();    
                        } catch (EntityException $e) {}
                        

                        $responseData['prize'] = array(
                            'id' => $prize->getId(),
                            'title' => $prize->getTitle(),
                            'image' => $prize->getImage(),
                        );
                    } else {
                        Session::connect()->get(Player::IDENTITY)->addPoints($gameObj->getPointsWin(), "Выигрыш в моментальный шанс");
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
            $this->ajaxResponse(array(), 0, 'INVALID_GAME');
        }
    }
}
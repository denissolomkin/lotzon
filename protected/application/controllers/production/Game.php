<?php

namespace controllers\production;
use \Application, \Config, \Player, \EntityException, \Session, \LotteryTicket, \LotteriesModel, \TicketsModel, \GameSettings, \GameSettingsModel;

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
        }
    }

    public function createTicketAction()
    {
        $respData = array();

        $ticket = new LotteryTicket();
        $ticket->setPlayerId(Session::connect()->get(Player::IDENTITY)->getId());
        $ticket->setCombination($this->request()->post('combination'));

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
                $data['tickets'][] = $ticket->getCombination();
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
                        $data['ticketWins'][] = $prize['sum'] . " баллов";
                    } else {
                        $data['ticketWins'][] = $prize['sum'] . " " . Config::instance()->langCurrencies[Session::connect()->get(Player::IDENTITY)->getCountry()];
                    }
                } else {
                    $data['ticketWins'][] = 0;
                }                
            }
            $this->ajaxResponse($data);
        } else {
            $this->ajaxResponse(array(
                'content' => false,
                'wait' => 1000,
            ));
        }
        $this->ajaxResponse(array(), 0, 'UNEXPECTED_ERROR');
    }
}
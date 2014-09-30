<?php

namespace controllers\production;
use \Application, \Config, \Player, \EntityException, \Session, \LotteryTicket;

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
}
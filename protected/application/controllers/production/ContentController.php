<?php

namespace controllers\production;
use \Application, \Config, \Player, \EntityException, \Session, \LotteryTicket, \LotteriesModel;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/entities/LotteryTicket.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class ContentController extends \AjaxController
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

    public function lotteriesAction() {
        $offset = $this->request()->get('offset');
        try {
            $lotteries = LotteriesModel::instance()->getPublishedLotteriesList(Index::LOTTERIES_PER_PAGE, $offset);
        } catch (EntityException $e) {
            $this->ajaxResponse(array(), 0, $e->getMessage());
        }
        $response = array(
            'lotteries'      => array(),
            'keepButtonShow' => true,
        );
        if (count($lotteries)) {
            foreach ($lotteries as $lottery) {
                $response['lotteries'][$lottery->getId()] = array(
                    'id'           => $lottery->getId(),
                    'date'         => $lottery->getDate('d.m.Y'),
                    'combination'  => $lottery->getCombination(),
                    'winnersCount' => $lottery->getWinnersCount(),
                );
            }
        }
        if (count($lotteries) < Index::LOTTERIES_PER_PAGE) {
            $response['keepButtonShow'] = false;
        }

        $this->ajaxResponse($response);
    }
}
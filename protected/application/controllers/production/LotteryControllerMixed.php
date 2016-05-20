<?php

namespace controllers\production;
use \Application, \SettingsModel, \Player, \EntityException, \LotteryTicket, \CountriesModel, \TicketsModel, \LotterySettings, \Common;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/entities/LotteryTicket.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class LotteryControllerMixed extends \AjaxController
{

    static $lotteriesPerPage;

    public function init()
    {
        self::$lotteriesPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('LOTTERIES_PER_PAGE') ? : 10;

        parent::init();

        $this->validateRequest();
    }

    public function historyAction()
    {
        if ($this->isAuthorized(true)) {
            $this->validateLogout();
            $this->validateCaptcha();
            $playerId = $this->player->getId();
        } else {
            $playerId = NULL;
        }

        $offset = $this->request()->get('offset');
        $count  = $this->request()->get('count', self::$lotteriesPerPage);

        $type = $this->request()->get('type');

        $response = array(
            'res' => array(
                'lottery' => array(
                    'history' => array(
                    ),
                ),
            ),
        );

        try {
            if ($type != "mine") {
                $list = \LotteriesModel::instance()->getPublishedLotteriesList($count + 1, $offset);
            } else {
                if ($playerId === NULL) {
                    $this->authorizedOnly();
                    return false;
                }
                $list = \LotteriesModel::instance()->getPlayerPlayedLotteries($playerId, $count + 1, $offset);
            }
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();

            return false;
        }

        if (count($list)<=$count) {
            $response['lastItem'] = true;
        } else {
            array_pop($list);
        }

        if ($type == "mine") {
            foreach ($list as $id => $lottery) {
                $response['res']['lottery']['history'][$id]         = $lottery->exportTo('list');
                $response['res']['lottery']['history'][$id]['type'] = "mine";
            }
        } else {
            if ($playerId !==NULL) {
                $mine = \LotteriesModel::instance()->isPlayerPlayedLotteries(array_keys($list), $playerId);
            } else {
                $mine = array();
            }
            foreach ($list as $id => $lottery) {
                $response['res']['lottery']['history'][$id]         = $lottery->exportTo('list');
                if (in_array($id,$mine)) {
                    $response['res']['lottery']['history'][$id]['type'] = "mine";
                } else {
                    $response['res']['lottery']['history'][$id]['type'] = "all";
                }
            }
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }

}

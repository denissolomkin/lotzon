<?php

namespace controllers\production;

use \Application, \Banner, \GamesPublishedModel, \SettingsModel, \Common;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class GamesControllerMixed extends \AjaxController
{

    public function init()
    {
        parent::init();
        $this->validateRequest();
    }

    public function listAction($key = NULL)
    {

        if ($this->isAuthorized(true)) {
            $this->validateLogout();
            $this->validateCaptcha();
            $country = $this->player->getCountry();
            $lang    = $this->player->getLang();
        } else {
            $country = Common::getUserIpCountry();
            $lang    = Common::getUserIpLang();
        }

        if (!$key) {
            $this->ajaxResponseBadRequest('EMPTY_GAMES_KEY');
        } else {
            $key = ucfirst($key) . ($key !== 'moment' ? 'Game' : '');
        }

        $response = array(
            'res' => array(
                'games' => array()
            ));

        switch ($key) {

            case "OnlineGame":

                try {
                    if (!($publishedGames = GamesPublishedModel::instance()->getList()[$key])) {
                        $this->ajaxResponse(array(), 0, 'NOT_PUBLISHED_GAMES');
                    }
                } catch (\PDOException $e) {
                    $this->ajaxResponse(array(), 0, 'INTERNAL_ERROR');
                }

                foreach ($publishedGames->getLoadedGames() as $id => $game) {

                    if (!isset($response['res']['games'][$game->getType()]))
                        $response['res']['games'][$game->getType()] = array();

                    if (!$game->isEnabled())
                        continue;

                    $game->setLang($lang);
                    $stat = $game->export('list');

                    if ($key == 'OnlineGame') {

                        $fund = \GameAppsModel::instance()->getFund($game->getId());
                        try {
                            $comission = $game->getOptions('r') ? $game->getOptions('r') / 100 : 0;
                        } catch (\EntityException $e) {
                            echo $e->getMessage();
                        }

                        if (!empty($fund)) {
                            foreach ($fund as $currency => &$total) {
                                $total = ($currency == 'POINT') ? ceil($total * $comission) : ceil($total * $comission * 100) / 100;
                            }
                        }

                        $stat += array(
                            'players' => \GamePlayersModel::instance()->getOnline($game->getId()),
                            'fund'    => $fund
                        );
                    }

                    $response['res']['games'][$game->getType()][] = $stat;

                }

                break;

            default:
                $this->ajaxResponseBadRequest('WRONG_GAME_KEY');
                break;

        }

        $banner = new Banner;
        $keys = array_keys($response['res']['games'][$game->getType()]);
        $response['res']['games'][$game->getType()][$keys[array_rand($keys)]]['block'] = $banner
            ->setDevice('desktop')
            ->setLocation('context')
            ->setPage('game')
            ->setCountry($country)
            ->random()
            ->render();

        $this->ajaxResponseNoCache($response);
    }

}
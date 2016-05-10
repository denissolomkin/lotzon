<?php

namespace controllers\production;

use \Application, \Banner, \GamesPublishedModel, \SettingsModel;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class GamesController extends \AjaxController
{

    public function init()
    {
        parent::init();
        $this->validateRequest();
        $this->authorizedOnly(true);
        $this->validateLogout();
        $this->validateCaptcha();
    }

    public function listAction($key = NULL)
    {

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

                    $game->setLang($this->player->getLang());
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
            ->setCountry($this->player->getCountry())
            ->random()
            ->render();

        $this->ajaxResponseNoCache($response);
    }

    public function itemAction($key = null, $id = null)
    {

        if (!$key) {
            $this->ajaxResponseBadRequest('EMPTY_GAMES_KEY');
        } else {
            $key = ucfirst($key) . ($key !== 'moment' ? 'Game' : '');
        }

        if (!$id) {
            $this->ajaxResponseBadRequest('GAME_ID_EMPTY');
        }

        if (!($publishedGames = GamesPublishedModel::instance()->getList()[$key])) {
            $this->ajaxResponseNotFound('EMPTY_PUBLISHED_GAMES');
        }

        switch ($key) {

            case 'OnlineGame':
                $game = $publishedGames->getLoadedGames()[array_search($id, $publishedGames->getGames())];
                break;

            default:
                $this->ajaxResponseBadRequest('WRONG_GAME_KEY');
                break;

        }


        if ($game) {

            if (!$game->isEnabled())
                $this->ajaxResponse(array(), 0, 'GAME_NOT_ENABLED');

            $response = array(
                'res' => array(
                    'games' => array()
                )
            );

            $game->setLang($this->player->getLang());

            $response['res']['games'][$game->getType()][$game->getId()] = $game->export('item');

            if ($key == 'OnlineGame') {

                $gamePlayer = new \GamePlayer();
                $gamePlayer
                    ->setId($this->player->getId())
                    ->fetch();
                $gamePlayer
                    ->formatFrom('player', $this->player);

                if (!$gamePlayer->getApp('Uid'))
                    $gamePlayer
                        ->setAppId($game->getId())
                        ->setAppName($game->getKey())
                        ->setAppMode(null);
                else
                    $response['player']['games']['online'] = array(
                        'Key' => $gamePlayer->getApp('Key'),
                        'Uid' => $gamePlayer->getApp('Uid'));

                $gamePlayer->update();

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

                $response['res']['games'][$game->getType()][$game->getId()]['fund'] = $fund;
            }

            $this->ajaxResponseNoCache($response);

        } else {
            $this->ajaxResponse(array(), 0, 'GAME_NOT_FOUND');
        }


    }

    public function nowAction($key = NULL, $id = null)
    {

        if (!$key) {
            $this->ajaxResponse(array(), 0, 'EMPTY_GAMES_KEY');
        }

        try {
            $gameApp = new \GameConstructor;
            $gameApp
                ->setId($id)
                ->setType('online')
                ->fetch();
            $gameApps  = \GameAppsModel::instance()->getList($id);
            $gameStack = \GamePlayersModel::instance()->getStack($id);
        } catch (\PDOException $e) {
            $this->ajaxResponse(array(), 0, 'INTERNAL_ERROR');
        }

        $response = array(
            'lastItem' => true,
            'res'      => array(
                'games' => array()
            )
        );

        $games = array();

        foreach ($gameApps as $uid => $game) {

            if ($game->isRun() || $game->isOver() || $game->getNumberPlayers() == count($game->getClients()))
                continue;

            $games[] = array(
                'id'        => $game->getUid(),
                'key'       => $game->getKey(),
                'mode'      => $game->getMode(), // $game->getCurrency() . '-' . $game->getPrice() . '-' . $game->getNumberPlayers(),
                'variation' => $game->getVariation(),
                'players'   => $game->getClients()
            );
        }

        foreach ($gameStack as $mode => $clients) {
            foreach ($clients as $clientId => $client) {
                $games[] = array(
                    'id'      => 0,
                    'key'     => $gameApp->getKey(),
                    'mode'    => $mode,
                    'players' => array($client->getName())
                );
            }
        }

        $response['res']['games'][$key][$id] = array(
            'now' => $games
        );

        $this->ajaxResponseNoCache($response);
    }

    public function ratingAction($key = NULL, $id = NULL)
    {

        if (!$key) {
            $this->ajaxResponse(array(), 0, 'EMPTY_GAMES_KEY');
        }

        $limit    = (int)SettingsModel::instance()->getSettings('counters')->getValue('RATING_PER_PAGE') ?: 5;
        $currency = $this->request()->get('currency', 'MONEY');
        $offset   = $this->request()->get('offset', NULL);

        try {

            $list = \GameAppsModel::instance()->getRating($id);
            if (isset($list[$currency])) {
                $list = $list[$currency];
                if ($limit) {
                    $list = array_slice($list, $offset, $limit + 1);
                }
            } else {
                $list = array();
            }

        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError('INTERNAL_ERROR');
        }

        $response = array(
            'res' => array(
                'games' => array()
            ));

        if (count($list) <= $limit) {
            $response['lastItem'] = true;
        } else {
            array_pop($list);
        }

        $response['res']['games'] = array(
            "$key" => array(
                "$id" => array(
                    'rating' => $list
                )));

        $this->ajaxResponseNoCache($response);
    }
}
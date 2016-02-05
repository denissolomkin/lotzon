<?php

namespace controllers\production;

use \Application, \Player, \GamesPublishedModel, \SettingsModel;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class GamesController extends \AjaxController
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
        $this->session->get(Player::IDENTITY)->markOnline();

        return true;
    }

    public function listAction($key = NULL)
    {
        $this->validateRequest();
        $this->authorizedOnly();

        if (!$key) {
            $this->ajaxResponseBadRequest('EMPTY_GAMES_KEY');
        } else {
            $key = ucfirst($key) . ($key !== 'moment' ? 'Game' : '');
        }

        $lang     = $this->session->get(Player::IDENTITY)->getLang();
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

                    if (!isset($response['games'][$game->getType()]))
                        $response['games'][$game->getType()] = array();

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

        $this->ajaxResponseCode($response);
    }

    public function itemAction($key = null, $id = null)
    {

        $this->validateRequest();
        $this->authorizedOnly();

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

            $game->setLang($this->session->get(Player::IDENTITY)->getLang());

            $response['res']['games'][$game->getType()][$game->getId()] = $game->export('item');

            if ($key == 'OnlineGame') {

                $gamePlayer = new \GamePlayer();
                $gamePlayer
                    ->setId($this->session->get(Player::IDENTITY)->getId())
                    ->fetch();
                $gamePlayer
                    ->formatFrom('player', $this->session->get(Player::IDENTITY));

                if (!$gamePlayer->getApp('Uid'))
                    $gamePlayer
                        ->setAppId($game->getId())
                        ->setAppName($game->getKey())
                        ->setAppMode(null);
                else
                    $response['player']['games']['online'] = array(
                        'key' => $gamePlayer->getApp('Key'),
                        'uid' => $gamePlayer->getApp('Uid'));

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

            $this->ajaxResponseCode($response);

        } else {
            $this->ajaxResponse(array(), 0, 'GAME_NOT_FOUND');
        }


    }

    public function nowAction($key = NULL, $id = null)
    {

        $this->validateRequest();
        $this->authorizedOnly();

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

            if ($game->isRun() || $game->isOver())
                continue;

            $players = array();

            foreach ($game->getPlayers() as $player) {
                $players[] = $player->name;
            }

            $games[] = array(
                'id'        => $uid,
                'key'       => $game->getApp()->getKey(),
                'mode'      => $game->getApp()->getCurrency() . '-' . $game->getApp()->getPrice() . '-' . $game->getApp()->getNumberPlayers(),
                'variation' => $game->getApp()->getVariation(),
                'players'   => $players
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

        $this->ajaxResponseCode($response);
    }

    public function ratingAction($key = NULL, $id = NULL)
    {

        $this->validateRequest();
        $this->authorizedOnly();

        if (!$key) {
            $this->ajaxResponse(array(), 0, 'EMPTY_GAMES_KEY');
        }

        $limit    = (int)SettingsModel::instance()->getSettings('counters')->getValue('RATING_PER_PAGE') ?: 10;
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

        $this->ajaxResponseCode($response);
    }
}
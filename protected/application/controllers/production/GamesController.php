<?php

namespace controllers\production;

use \Application, \Player, \GamesPublishedModel, \SettingsModel;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class GamesController extends \AjaxController
{
    private $session;

    static $ratingPerPage;

    public function init()
    {
        self::$ratingPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('RATING_PER_PAGE') ?: 10;

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
            $this->ajaxResponse(array(), 0, 'EMPTY_GAMES_KEY');
        } else {
            $key = ucfirst($key) . 'Game';
        }

        $lang = $this->session->get(Player::IDENTITY)->getLang();

        try {

            if (!($publishedGames = GamesPublishedModel::instance()->getList()[$key])) {
                $this->ajaxResponse(array(), 0, 'NOT_PUBLISHED_GAMES');
            }

        } catch (\PDOException $e) {
            $this->ajaxResponse(array(), 0, 'INTERNAL_ERROR');
        }

        $response = array(
            'res' => array(
                'games' => array()
            )
        );

        foreach ($publishedGames->getLoadedGames() as $id => $game) {

            if (!isset($response['games'][$game->getType()]))
                $response['games'][$game->getType()] = array();

            if (!$game->isEnabled())
                continue;

            $game->setLang($lang);
            $stat = $game->export('list');

            if ($key == 'OnlineGame') {

                $fund = \OnlineGamesModel::instance()->getFund($game->getId());
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

        $this->ajaxResponseCode($response);
    }

    public function itemAction($key = null, $id = null)
    {

        $this->validateRequest();
        $this->authorizedOnly();

        if (!$key) {
            $this->ajaxResponse(array(), 0, 'EMPTY_GAMES_KEY');
        } else {
            $key = ucfirst($key) . 'Game';
        }

        $publishedGames = GamesPublishedModel::instance()->getList()[$key];

        if (!$publishedGames) {
            $this->ajaxResponse(array(), 0, 'NOT_PUBLISHED_GAMES');
        }

        switch ($key) {
            case 'ChanceGame':
            case 'OnlineGame':

                if (!$id) {
                    $this->ajaxResponse(array(), 0, 'GAME_ID_EMPTY');
                }

                $game = $publishedGames->getLoadedGames()[array_search($id, $publishedGames->getGames())];
                break;

            case 'QuickGame':
            case 'Moment':
                $game = $publishedGames->getLoadedGames(array_rand($publishedGames->getGames()));
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
                    ->formatFrom('player', $this->session->get(Player::IDENTITY))
                    ->setAppId($game->getId())
                    ->setAppName($game->getKey())
                    ->update();

                $fund = \OnlineGamesModel::instance()->getFund($game->getId());
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

    public function nowAction($key = NULL)
    {

        $this->validateRequest();
        $this->authorizedOnly();

        if (!$key) {
            $this->ajaxResponse(array(), 0, 'EMPTY_GAMES_KEY');
        }

        try {
            $gameApps  = \GameAppsModel::instance()->getList($key);
            $gameStack = \GamePlayersModel::instance()->getStack($key);
        } catch (\PDOException $e) {
            $this->ajaxResponse(array(), 0, 'INTERNAL_ERROR');
        }

        $response = array(
            'res' => array(
                'games' => array()
            )
        );

        $games = array();

        foreach ($gameApps as $id => $game) {

            $players = array();

            foreach ($game->getPlayers() as $player) {
                $players[] = $player->name;
            }

            $games[] = array(
                'id'        => $id,
                'mode'      => $game->getApp()->getCurrency() . '-' . $game->getApp()->getPrice() . '-' . $game->getApp()->getNumberPlayers(),
                'variation' => $game->getApp()->getVariation(),
                'players'   => $players
            );
        }

        foreach ($gameStack as $mode => $clients) {
            foreach ($clients as $id => $client) {
                $games[] = array(
                    'id'      => 0,
                    'mode'    => $mode,
                    'players' => array($client->getName())
                );
            }
        }

        $response['res']['games'][$key] = array(
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

        $count  = $this->request()->get('count', self::$ratingPerPage);
        $offset = $this->request()->get('offset', NULL);

        try {
            $rating = \OnlineGamesModel::instance()->getRating($id, $count, $offset);
        } catch (\PDOException $e) {
            $this->ajaxResponse(array(), 0, 'INTERNAL_ERROR');
        }

        $response = array(
            'res' => array(
                'games' => array()
            )
        );

        $response['res']['games'][$key] = array();

        $response['res']['games'][$key][$id] = array(
            'rating' => $rating
        );

        $this->ajaxResponseCode($response);
    }
}
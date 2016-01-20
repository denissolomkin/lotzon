<?php

namespace controllers\production;

use \Application, \Player, \EntityException, \Banner, \CountriesModel, \LotterySettings, \QuickGamesModel;
use \ChanceGamesModel, \GamesPublishedModel, \SettingsModel;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/entities/Banner.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class ChanceController extends \AjaxController
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

        return true;
    }

    public function listAction($key)
    {

        $this->validateRequest();
        $this->authorizedOnly();

        if (!$key) {
            $this->ajaxResponseBadRequest('EMPTY_GAMES_KEY');
        }

        $lang = $this->session->get(Player::IDENTITY)->getLang();

        try {
            if (!($publishedGames = GamesPublishedModel::instance()->getList()[$key])) {
                $this->ajaxResponseNotFound('NOT_PUBLISHED_GAMES');
            }
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
        }

        $response = array(
            'res' => array(
                'games' => array()
            ));

        foreach ($publishedGames->getLoadedGames() as $id => $game) {

            if (!isset($response['games'][$game->getType()]))
                $response['games'][$game->getType()] = array();

            if (!$game->isEnabled())
                continue;

            $game->setLang($lang);
            $response['res']['games'][$game->getType()][] = $game->export('list');
        }

        $this->ajaxResponseCode($response);
    }

    public function itemAction($key = 'QuickGame', $id = null)
    {

        $this->validateRequest();
        $this->authorizedOnly();

        $publishedGames = GamesPublishedModel::instance()->getList()[$key];

        if (!$publishedGames || !is_array($publishedGames->getGames())) {
            $this->ajaxResponse(array(), 0, 'GAME_NOT_ENABLED');
        }

        switch ($key) {
            case 'ChanceGame':
                break;
            case 'QuickGame':
            case 'Moment':
                $id = $publishedGames->getGames()[array_rand($publishedGames->getGames())];
                break;
        }

        if (!$id) {
            $this->ajaxResponse(array(), 0, 'GAME_LIST_EMPTY');
        }

        $lang = $this->session->get(Player::IDENTITY)->getLang();

        if ($game = QuickGamesModel::instance()->getList()[$id]) {

            $game->setKey($key)
                ->setLang($lang)
                ->loadPrizes();

            $response = array(
                'res' => array(
                    'games' => array(
                        'chance' => array()
                    )
                )
            );

            $response['res']['games']['chance'][$game->getId()] = $game->export('item');
            $this->ajaxResponseCode($response);

        }

        $this->ajaxResponse(array(), 0, 'GAME_NOT_FOUND');
    }

    public function startAction($key = 'QuickGame', $id = null)
    {

        $this->validateRequest();
        $this->authorizedOnly();

        $publishedGames = GamesPublishedModel::instance()->getList()[$key];
        $response       = array();

        switch ($key) {
            case 'ChanceGame':
                break;
            case 'QuickGame':
            case 'Moment':
                $id = $publishedGames->getGames()[array_rand($publishedGames->getGames())];
                break;
        }

        switch (true) {
            case !($player = $this->session->get(Player::IDENTITY)):
                $error = 'PLAYER_NOT_FOUND';
                break;

            case !$id:
                $error = 'GAME_LIST_EMPTY';
                break;

            case !$publishedGames:
                $error = 'GAME_NOT_ENABLED';
                break;

            case $publishedGames->getOption('min') && $this->session->get($key . 'LastDate') + $publishedGames->getOption('min') * 60 > time():
                $error = 'TIME_NOT_YET';
                break;

            default:
                $error = false;
                break;
        }

        if ($error)
            $this->ajaxResponseBadRequest($error);

        if ($this->session->has($key) && $game = $this->session->get($key)) {
            $response['res'] = $game->export('stat');

        } elseif ($game = QuickGamesModel::instance()->getList()[$id]) {

            if ($game->getOption('p'))
                if ($player->getBalance()['Points'] < $game->getOption('p'))
                    $this->ajaxResponseBadRequest('INSUFFICIENT_FUNDS');
                else
                    $player->addPoints($game->getOption('p') * -1, $game->getTitle($player->getLang()));

            $response['player'] = array(
                "balance" => array(
                    "points" => $player->getPoints()
                )
            );

            $game->setUserId($player->getId())
                ->setTimeout($publishedGames->getOption('timeout'))
                ->setTime(time())
                ->setKey($key)
                ->setLang($player->getLang())
                ->setUid(uniqid())
                ->loadPrizes()
                ->saveGame();

            while (!$this->session->has($key))
                $this->session->set($key, $game);

            $response['res'] = $game->export('stat');
        }

        if (isset($game)) {

            $banner                   = new Banner;
            $response['res']['block'] = $banner
                ->setGroup('game' . $game->getId())
                ->setCountry($player->getCountry())
                ->setTemplate('chance')
                ->setKey($key)
                ->random()
                ->render();
        }

        if ($this->session->has($key))
            $this->ajaxResponseCode($response);
        else
            $this->ajaxResponseBadRequest('GAME_NOT_ENABLED');
    }

    public function playAction($key = 'QuickGame')
    {

        $this->validateRequest();
        $this->authorizedOnly();

        switch (true) {
            case !($player = $this->session->get(Player::IDENTITY)):
                $error = 'PLAYER_NOT_FOUND';
                break;

            case !$this->session->has($key):
                $error = 'GAME_NOT_FOUND';
                break;

            case !($cell = $this->request()->get('cell', null)):
                $error = 'CELL_NOT_SELECT';
                break;

            case !($game = $this->session->get($key)):
                $error = 'WRONG_GAME';
                break;

            case $game->isOver():
                $error = 'GAME_IS_OVER';
                $this->session->remove($key);
                break;

            default:
                $error = false;
                break;
        }

        if ($error)
            $this->ajaxResponseBadRequest($error);

        $response = $game->doMove($cell);

        if ($game->isOver()) {

            $this->session->set($key . 'LastDate', time());
            $this->session->remove($key);
            $this->session->remove($key . 'Important');

            if ($player->checkLastGame($key)) {
                if ($game->getGamePrizes())
                    foreach ($game->getGamePrizes() as $currency => $sum)
                        if ($sum) {
                            if ($currency == LotterySettings::CURRENCY_MONEY) {
                                $sum *= CountriesModel::instance()->getCountry($player->getCountry())->loadCurrency()->getCoefficient();
                                $player->addMoney($sum, "Выигрыш " . $game->getTitle($player->getLang()));
                            } elseif ($currency == LotterySettings::CURRENCY_POINT)
                                $player->addPoints($sum, "Выигрыш " . $game->getTitle($player->getLang()));
                        }

                $response['player'] = array(
                    "balance" => array(
                        "points" => $player->getPoints(),
                        "money"  => $player->getMoney()
                    ));

            } else {
                $player->writeLog(array('action' => 'CHEAT', 'desc' => $key, 'status' => 'danger'));
                $this->ajaxResponseBadRequest('CHEAT_GAME');
            }

        }

        $this->ajaxResponseCode($response);
    }
}
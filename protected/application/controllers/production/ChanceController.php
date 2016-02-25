<?php

namespace controllers\production;

use \Application, \Player, \Banner, \LotterySettings;
use \GamesPublishedModel, \CountriesModel, \GameConstructorChance;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/entities/Banner.php');
Application::import(PATH_APPLICATION . 'model/entities/GameConstructorChance.php');
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

        foreach ($publishedGames->getLoadedGames() as $game) {

            if (!$game->isEnabled())
                continue;

            if (!isset($response['res']['games'][$game->getType()]))
                $response['res']['games'][$game->getType()] = array();

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

        if ($game = $publishedGames->getLoadedGames()[array_search($id, $publishedGames->getGames())]) {

            $game
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

        /* validate id */
        switch ($key) {
            case 'ChanceGame':
                break;
            case 'QuickGame':
            case 'Moment':
                $id = $publishedGames->getGames()[array_rand($publishedGames->getGames())];
                break;
        }

        /* validate errors */
        switch (true) {
            case !($player = $this->session->get(Player::IDENTITY)):
                $error = 'PLAYER_NOT_FOUND';
                break;

            case !$id:
                $error = 'GAME_LIST_EMPTY';
                break;

            case !$publishedGames:
                $error = 'GAMES_NOT_ENABLED';
                break;

            case $publishedGames->getOptions('min') && $this->session->get($key . 'LastDate') + $publishedGames->getOptions('min') * 60 > time():
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

        } elseif ($gameConstructor = $publishedGames->getLoadedGames()[array_search($id, $publishedGames->getGames())]) {


            $game = new GameConstructorChance();
            $game
                ->setType($gameConstructor->getType())
                ->setId($gameConstructor->getId())
                ->fetch();

            $game->setUserId($player->getId())
                ->setTimeout($publishedGames->getOptions('timeout'))
                ->setTime(time())
                ->setLang($player->getLang())
                ->setUid(uniqid())
                ->loadPrizes();

            $balance = $player->getBalance();

            if ($game->getOptions('p')) {

                if ($balance['Points'] < $game->getOptions('p'))
                    $this->ajaxResponseBadRequest('INSUFFICIENT_FUNDS');

                else {
                    $player->addPoints(
                        $game->getOptions('p') * -1,
                        array(
                            'id' => $game->getUid(),
                            'object' => $key,
                            'title' => $game->getTitle($player->getLang())
                        )
                    );
                }
            }

            $response['res'] = $game->export('stat');

            /* todo */
            $game->saveGame();

            $balance = $player->getBalance();
            $response['player'] = array(
                "balance" => array(
                    "points" => $balance['Points'],
                    "money" => $balance['Money']
                )
            );

            if (!$game->isOver()) {
                while (!$this->session->has($key))
                    $this->session->set($key, $game);

            }

        } else {
            $this->ajaxResponseBadRequest('GAME_NOT_ENABLED');
        }

        $banner = new Banner;
        $response['res']['block'] = $banner
            ->setGroup('game' . $game->getId())
            ->setCountry($player->getCountry())
            ->setTemplate('chance')
            ->setKey($key=='Moment'?'moment':'random')
            ->random()
            ->render();

        $this->ajaxResponseCode($response);
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

                $this->playerAward($player, $game);
                $response['player'] = array(
                    "balance" => array(
                        "points" => $player->getPoints(),
                        "money"  => $player->getMoney()
                    ));

            } else {
                $player->writeLog(array('action' => 'CHEAT', 'desc' => $key, 'status' => 'danger'));
                $this->ajaxResponseBadRequest('CHEAT_GAME');
            }

        } else {

            $this->session->set($key, $game);
        }

        $this->ajaxResponseCode($response);
    }

    private function playerAward($player, $game)
    {

        foreach ($game->getGamePrizes() as $currency => $sum) {
            if ($sum) {
                switch ($currency) {

                    case LotterySettings::CURRENCY_MONEY:
                        $sum *= CountriesModel::instance()->getCountry($player->getCountry())->loadCurrency()->getCoefficient();
                        $player->addMoney(
                            $sum,
                            array(
                                'id' => $game->getUid(),
                                'object' => 'Chance',
                                'title' => "Выигрыш " . $game->getTitle($player->getLang())
                            ));
                        break;

                    case LotterySettings::CURRENCY_POINT:
                        $player->addPoints(
                            $sum,
                            array(
                                'id' => $game->getUid(),
                                'object' => 'Chance',
                                'title' => "Выигрыш " . $game->getTitle($player->getLang())
                            ));
                        break;
                }
            }
        }
    }
}
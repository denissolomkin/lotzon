<?php

namespace controllers\production;

use \Application, \Banner, \LotterySettings;
use \GamesPublishedModel, \CountriesModel, \GameConstructorChance;

Application::import(PATH_APPLICATION . 'model/entities/GameConstructorChance.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class ChanceController extends \AjaxController
{

    public function init()
    {
        parent::init();
        $this->validateRequest();
        $this->authorizedOnly(true);
        $this->validateLogout();
        $this->validateCaptcha();
    }

    public function itemAction($key = 'QuickGame', $id = null)
    {

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

        if ($game = $publishedGames->getLoadedGames()[array_search($id, $publishedGames->getGames())]) {

            $game
                ->setLang($this->player->getLang())
                ->loadPrizes();

            $response = array(
                'res' => array(
                    'games' => array(
                        'chance' => array()
                    )
                )
            );

            $response['res']['games']['chance'][$game->getId()] = $game->export('item');
            $this->ajaxResponseNoCache($response);

        }

        $this->ajaxResponse(array(), 0, 'GAME_NOT_FOUND');
    }

    public function startAction($key = 'QuickGame', $id = null)
    {

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

            $game->setUserId($this->player->getId())
                ->setTimeout($publishedGames->getOptions('timeout'))
                ->setTime(time())
                ->setLang($this->player->getLang())
                ->setUid(uniqid())
                ->setKey($key)
                ->loadPrizes();

            $balance = $this->player->getBalance();

            if ($game->getOptions('p')) {

                if ($balance['Points'] < $game->getOptions('p'))
                    $this->ajaxResponseBadRequest('INSUFFICIENT_FUNDS');

                else {

                    $desc = array(
                        'id'    => $game->getId(),
                        'uid'   => $game->getUid(),
                        'type'  => $game->getKey(),
                        'title' => $game->getTitle($this->player->getLang())
                    );

                    $this->player->addPoints(
                        $game->getOptions('p') * -1,
                        $desc
                    );
                }
            }

            $response['res'] = $game->export('stat');
            $response['res']['Key'] = str_replace('Game','',$key);

            /* todo */
            $game->saveGame();

            $balance = $this->player->getBalance();
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
            ->setDevice('desktop')
            ->setLocation('chance')
            ->setPage($game->getId())
            ->setCountry($this->player->getCountry())
            ->setTemplate('chance')
            ->setKey($key=='Moment'?'moment':'random')
            ->random()
            ->render();

        $this->ajaxResponseNoCache($response);
    }

    public function playAction($key = 'QuickGame')
    {

        switch (true) {

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

            if ($this->player->checkDate($key)) {

                $this->playerAward($game);
                $response['player'] = array(
                    "balance" => array(
                        "points" => $this->player->getPoints(),
                        "money"  => $this->player->getMoney()
                    ));

                $response['captcha'] = $this->player->activateCaptcha();

            } else {
                $this->player->writeLog(array('action' => 'CHEAT', 'desc' => $key, 'status' => 'danger'));
                $this->ajaxResponseBadRequest('CHEAT_GAME');
            }

        } else {

            $this->session->set($key, $game);
        }

        $this->ajaxResponseNoCache($response);
    }

    private function playerAward($game)
    {

        foreach ($game->getGamePrizes() as $currency => $sum) {

            if ($sum) {

                $desc = array(
                    'id'    => $game->getId(),
                    'uid'   => $game->getUid(),
                    'type'  => $game->getKey(),
                    'title' => "Выигрыш " . $game->getTitle($this->player->getLang())
                );

                switch ($currency) {

                    case LotterySettings::CURRENCY_MONEY:
                        $sum *= CountriesModel::instance()->getCountry($this->player->getCurrency())->loadCurrency()->getCoefficient();
                        $this->player->addMoney(
                            $sum,
                            $desc
                        );
                        break;

                    case LotterySettings::CURRENCY_POINT:
                        $this->player->addPoints(
                            $sum,
                            $desc
                        );
                        break;
                }
            }
        }
    }
}
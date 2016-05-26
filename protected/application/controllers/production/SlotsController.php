<?php

namespace controllers\production;

use \Application, \LotterySettings;
use \GamesPublishedModel, \GameConstructorSlots;

Application::import(PATH_APPLICATION . 'model/entities/GameConstructorSlots.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class SlotsController extends \AjaxController
{

    public function init()
    {
        parent::init();
        $this->validateRequest();
        $this->authorizedOnly(true);
        $this->validateCaptcha();
    }

    public function itemAction($key = 'ChanceGame', $id = null)
    {

        $publishedGames = GamesPublishedModel::instance()->getList()[$key];

        if (!$publishedGames || !is_array($publishedGames->getGames())) {
            $this->ajaxResponse(array(), 0, 'GAME_NOT_ENABLED');
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
                        'slots' => array()
                    )
                )
            );

            $response['res']['games']['slots'][$game->getId()] = $game->export('item');
            $this->ajaxResponseNoCache($response);

        }

        $this->ajaxResponse(array(), 0, 'GAME_NOT_FOUND');
    }

    public function startAction($key = 'ChanceGame', $id = null)
    {

        $publishedGames = GamesPublishedModel::instance()->getList()[$key];
        $response       = array();

        /* validate errors */
        switch (true) {

            case !$id:
                $error = 'GAME_LIST_EMPTY';
                break;

            case !$publishedGames:
                $error = 'GAMES_NOT_ENABLED';
                break;

            default:
                $error = false;
                break;
        }

        if ($error)
            $this->ajaxResponseBadRequest($error);

        if ($gameConstructor = $publishedGames->getLoadedGames()[array_search($id, $publishedGames->getGames())]) {

            $game = new GameConstructorSlots();
            $game
                ->setType($gameConstructor->getType())
                ->setId($gameConstructor->getId())
                ->fetch();

            $game->setUserId($this->player->getId())
                ->setTimeout($publishedGames->getOptions('timeout'))
                ->setTime(time())
                ->setLang($this->player->getLang())
                ->setUid(uniqid())
                ->loadPrizes();

            $balance = $this->player->getBalance();

            $currency = $this->request()->post('currency', null);
            $bet = $this->request()->post('bet', null);

            /* validate */
            switch (true) {

                case !$currency:
                    $this->ajaxResponseBadRequest('BAD_CURRENCY');
                    break;

                case (!$bet || $bet <= 0):
                    $this->ajaxResponseBadRequest('BAD_BET');
                    break;

                default:
                    switch ($currency) {

                        case LotterySettings::CURRENCY_MONEY:
                            $currencyBD = 'Money';
                            break;

                        case LotterySettings::CURRENCY_POINT:
                            $currencyBD = 'Points';
                            break;

                        default:
                            $this->ajaxResponseBadRequest('UNAVAILABLE_CURRENCY');
                            break;
                    }
                    break;
            }

            $min_max_array = array(
                'Money' => array(
                    'min' => 0.1,
                    'max' => 1,
                ),
                'Points' => array(
                    'min' => 1,
                    'max' => 10,
                ),
            );

            if (($bet < $min_max_array[$currencyBD]['min'])or($bet > $min_max_array[$currencyBD]['max'])) {
                $this->ajaxResponseBadRequest('BAD_BET');
            }

            if ($balance[$currencyBD] < $bet)
                $this->ajaxResponseBadRequest('INSUFFICIENT_FUNDS');

            $desc = array(
                'id'    => $game->getId(),
                'uid'   => $game->getUid(),
                'type'  => 'Slots',
                'title' => $game->getTitle($this->player->getLang())
            );

            $this->player->{'add' . $currencyBD}(
                $bet * -1,
                $desc
            );

            $response['res'] = $game
                ->setCurrency($currency)
                ->setBet($bet)
                ->doMove();

            $balance = $this->player->getBalance();

            /* todo saveGame */
            $game->saveGame();
            $this->playerAward($game);
            $this->player->updateSession();

            $response['captcha'] = $this->player->activateCaptcha();
            $response['player'] = array(
                'balance' => array(
                    'points' => $balance['Points'],
                    'money' => $balance['Money']
                )
            );


        } else {
            $this->ajaxResponseBadRequest('GAME_NOT_ENABLED');
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
                    'type'  => 'Slots',
                    'title' => 'Выигрыш ' . $game->getTitle($this->player->getLang())
                );

                switch ($currency) {

                    case LotterySettings::CURRENCY_MONEY:
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
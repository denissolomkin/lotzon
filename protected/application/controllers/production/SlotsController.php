<?php

namespace controllers\production;

use \Application, \Player, \LotterySettings;
use \GamesPublishedModel, \GameConstructorSlots;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/entities/GameConstructorSlots.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class SlotsController extends \AjaxController
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

    public function itemAction($key = 'ChanceGame', $id = null)
    {

        $this->validateRequest();
        $this->authorizedOnly();

        $publishedGames = GamesPublishedModel::instance()->getList()[$key];

        if (!$publishedGames || !is_array($publishedGames->getGames())) {
            $this->ajaxResponse(array(), 0, 'GAME_NOT_ENABLED');
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

        $this->validateRequest();
        $this->authorizedOnly();

        $publishedGames = GamesPublishedModel::instance()->getList()[$key];
        $response       = array();

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

            $game->setUserId($player->getId())
                ->setTimeout($publishedGames->getOptions('timeout'))
                ->setTime(time())
                ->setLang($player->getLang())
                ->setUid(uniqid())
                ->loadPrizes();

            $balance = $player->getBalance();

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

            if ($balance[$currencyBD] < $bet)
                $this->ajaxResponseBadRequest('INSUFFICIENT_FUNDS');

            $player->{'add' . $currencyBD}(
                $bet * -1,
                array(
                    'id' => $game->getUid(),
                    'object' => $key,
                    'title' => $game->getTitle($player->getLang())
                ));

            $response['res'] = $game
                ->setCurrency($currency)
                ->setBet($bet)
                ->doMove();

            /* todo */
            $game->saveGame();

            $balance = $player->getBalance();
            $response['player'] = array(
                "balance" => array(
                    "points" => $balance['Points'],
                    "money" => $balance['Money']
                )
            );

            $this->playerAward($player, $game);

        } else {
            $this->ajaxResponseBadRequest('GAME_NOT_ENABLED');
        }

        $this->ajaxResponseNoCache($response);
    }

    private function playerAward($player, $game)
    {

        foreach ($game->getGamePrizes() as $currency => $sum) {
            if ($sum) {
                switch ($currency) {

                    case LotterySettings::CURRENCY_MONEY:
                        $player->addMoney(
                            $sum,
                            array(
                                'id' => $game->getUid(),
                                'object' => 'Slots',
                                'title' => "Выигрыш " . $game->getTitle($player->getLang())
                            ));
                        break;

                    case LotterySettings::CURRENCY_POINT:
                        $player->addPoints(
                            $sum,
                            array(
                                'id' => $game->getUid(),
                                'object' => 'Slots',
                                'title' => "Выигрыш " . $game->getTitle($player->getLang())
                            ));
                        break;
                }
            }
        }
    }
}
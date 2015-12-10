<?php

namespace controllers\production;

use \Application, \Player, \EntityException, \CountriesModel, \LotterySettings, \QuickGamesModel;
use \ChanceGamesModel, \GameSettingsModel;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class ChanceController extends \AjaxController
{
    private $session;
    static $chancesPerPage;

    public function init()
    {
        self::$chancesPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('CHANCES_PER_PAGE') ?: 10;
        $this->session = new Session();
        parent::init();
        if ($this->validRequest()) {
            if (!$this->session->get(Player::IDENTITY) instanceof PLayer) {
                $this->ajaxResponse(array(), 0, 'NOT_AUTHORIZED');
            }
            $this->session->get(Player::IDENTITY)->markOnline();
        }
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

    public function listAction()
    {

        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $count = $this->request()->get('count', self::$messagesPerPage);
        $beforeId = $this->request()->get('before_id', NULL);
        $afterId = $this->request()->get('after_id', NULL);
        $offset = $this->request()->get('offset', NULL);

        try {
            $list = MessagesModel::instance()->getList($count, $beforeId, $afterId, $offset);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $resp = array(
            'res' => array(
                'games' => array(
                    'chance' => array()
                )
            )
        );

        foreach ($list as $id => $chance) {
            $response['res']['games']['chance'][$chance->getId()] = $chance->export('talk');
        }

        $this->ajaxResponse($resp);
    }

    public function previewAction($key = 'QuickGame')
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $id = $key == 'ChanceGame' ? $this->request()->get('id', null) : null;
        $settings = GameSettingsModel::instance()->getSettings($key);
        $player = $this->session->get(Player::IDENTITY);

        if (!$settings) {
            $this->ajaxResponse(array(), 0, 'GAME_NOT_ENABLED');
        } elseif (is_array($settings->getGames()) &&
            $game = QuickGamesModel::instance()->getList()[$id ?: $settings->getGames()[array_rand($settings->getGames())]]
        ) {

            $game->setKey($key)
                ->setLang($player->getLang())
                ->loadPrizes();

            $resp = $game->getStat();
            $this->ajaxResponse($resp);

        }

        $this->ajaxResponse(array(), 0, 'GAME_NOT_FOUND');
    }

    public function startAction($key = 'QuickGame')
    {

        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $id = $key == 'ChanceGame' ? $this->request()->get('id', null) : null;
        $player = $this->session->get(Player::IDENTITY);
        $settings = GameSettingsModel::instance()->getSettings($key);

        if (!$settings) {
            $this->ajaxResponse(array(), 0, 'GAME_NOT_ENABLED');

        } elseif ($settings->getOption('min') && $this->session->get($key . 'LastDate') + $settings->getOption('min') * 60 > time()) {
            $this->ajaxResponse(array(), 0, 'TIME_NOT_YET');

        } elseif ($this->session->has($key) && $game = $this->session->get($key)) {
            $resp = $game->getStat();

        } elseif (is_array($settings->getGames()) && $game = QuickGamesModel::instance()->getList()[$id ?: $settings->getGames()[array_rand($settings->getGames())]]) {

            if ($game->getOption('p'))
                if ($player->getBalance()['Points'] < $game->getOption('p'))
                    $this->ajaxResponse(array(), 0, 'INSUFFICIENT_FUNDS');
                else
                    $player->addPoints($game->getOption('p') * -1, $game->getTitle($player->getLang()));

            $game->setUserId($player->getId())
                ->setTimeout($settings->getOption('timeout'))
                ->setTime(time())
                ->setKey($key)
                ->setLang($player->getLang())
                ->setUid(uniqid())
                ->loadPrizes()
                ->saveGame();

            while (!$this->session->has($key))
                $this->session->set($key, $game);

            $resp = $game->getStat();
        }

        if (isset($game)) {

            $banner = new \Banner;
            $resp['block'] = $banner
                ->setGroup('game' . $game->getId())
                ->setCountry($player->getCountry())
                ->setTemplate('chance')
                ->setKey($key)
                ->random()
                ->render();
        }

        if ($this->session->has($key))
            $this->ajaxResponse($resp);
        else
            $this->ajaxResponse(array(), 0, 'GAME_NOT_ENABLED');
    }

    public function playAction($key = 'QuickGame')
    {

        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $error = false;

        if (!($player = $this->session->get(Player::IDENTITY))) {

        } elseif (!$this->session->has($key)) {
            $error = 'GAME_NOT_FOUND';
        } elseif (!($cell = $this->request()->post('cell', null))) {
            $error = 'CELL_NOT_SELECT';
        } elseif (!($game = $this->session->get($key))) {
            $error = 'WRONG_GAME';
        } elseif ($game->isOver()) {
            $error = 'GAME_IS_OVER';
            $this->session->remove($key);
        }

        if ($error) {
            $this->ajaxResponse(array(), 0, 'PLAYER_NOT_FOUND');
        }

        $res = $game->doMove($cell);

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
            } else {
                $player->writeLog(array('action' => 'CHEAT', 'desc' => $key, 'status' => 'danger'));
                $this->ajaxResponse(array(), 0, 'CHEAT_GAME');
            }

        }

        $this->ajaxResponse($res);
    }
}
<?php

namespace controllers\production;

use \Application, \Player;
use \ChanceGamesModel, \SettingsModel;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/entities/Banner.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class FlashController extends \AjaxController
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

    public function listAction()
    {

        $this->validateRequest();
        $this->authorizedOnly();

        $list = SettingsModel::instance()->getSettings('flashGames')->getValue();

        $response = array(
            'res' => array(
                'games' => array(
                    'flash' => $list
                )
            ));

        $this->ajaxResponseNoCache($response);
    }

    public function itemAction($id = null)
    {
        $this->validateRequest();
        $this->authorizedOnly();

        if (!$id) {
            $this->ajaxResponseBadRequest('GAME_ID_EMPTY');
        }

        $game = SettingsModel::instance()->getSettings('flashGames')->getValue()[$id];

        if(!$game)
            $this->ajaxResponseNotFound('GAME_NOT_FOUND');

        $response = array(
            'res' => array(
                'games' => array(
                    'flash' => array(
                        "$id" => $game
                    ))));

        $this->ajaxResponseNoCache($response);

    }
}
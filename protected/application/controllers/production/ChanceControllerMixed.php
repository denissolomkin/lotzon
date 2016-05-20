<?php

namespace controllers\production;

use \Application, \Banner, \LotterySettings;
use \GamesPublishedModel, \CountriesModel, \GameConstructorChance, \Common;

Application::import(PATH_APPLICATION . 'model/entities/GameConstructorChance.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class ChanceControllerMixed extends \AjaxController
{

    public function init()
    {
        parent::init();
        $this->validateRequest();
    }

    public function listAction($key)
    {

        if ($this->isAuthorized(true)) {
            $this->validateLogout();
            $this->validateCaptcha();
            $country = $this->player->getCountry();
            $lang    = $this->player->getLang();
        } else {
            $country = Common::getUserIpCountry();
            $lang    = Common::getUserIpLang();
        }

        if (!$key) {
            $this->ajaxResponseBadRequest('EMPTY_GAMES_KEY');
        }

        try {
            $publishedGames = GamesPublishedModel::instance()->getList()[$key];
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
        }

        if (!$publishedGames) {
            $this->ajaxResponseNotFound('NOT_PUBLISHED_GAMES');
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

        $banner = new Banner;
        $keys = array_keys($response['res']['games'][$game->getType()]);
        $response['res']['games'][$game->getType()][$keys[array_rand($keys)]]['block'] = $banner
            ->setDevice('desktop')
            ->setLocation('context')
            ->setPage('game')
            ->setCountry($country)
            ->random()
            ->render();

        $this->ajaxResponseNoCache($response);
    }

}
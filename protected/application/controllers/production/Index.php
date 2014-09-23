<?php

namespace controllers\production;
use \GameSettingsModel, \StaticSiteTextsModel, \Application, \Config;

Application::import(PATH_APPLICATION . '/model/models/GameSettingsModel.php');
Application::import(PATH_APPLICATION . '/model/models/StaticSiteTextsModel.php');


class Index extends \SlimController\SlimController 
{
    public $promoLang = '';

    public function indexAction()
    {
        $this->promoLang = Config::instance()->defaultLang;
        $this->landing();        
    }

    protected function landing()
    {
        $gameSettings = GameSettingsModel::instance()->loadSettings();

        $gameInfo = array(
            'participants' => 0,
            'winners'      => 0,
            'win'          => 0,
            'nextLottery'  => $gameSettings->getNearestGame() + strtotime('00:00:00', time()) - time(),
            'lotteryWins'  => $gameSettings->getPrizes($this->promoLang),
        );

        $staticTexts = $list = StaticSiteTextsModel::instance()->getListGroupedByIdentifier();

        $this->render('production/landing', array(
            'gameInfo'    => $gameInfo,
            'staticTexts' => $staticTexts,
            'lang'        => $this->promoLang,
            'currency'    => Config::instance()->langCurrencies[$this->promoLang],
            'layout'      => false,
        ));
    }
}
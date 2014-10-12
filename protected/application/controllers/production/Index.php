<?php

namespace controllers\production;
use \GameSettingsModel, \StaticSiteTextsModel, \Application, \Config, \Player, \Session, \PlayersModel, \ShopModel, \NewsModel;
use \TicketsModel, \LotteriesModel;

Application::import(PATH_APPLICATION . '/model/models/GameSettingsModel.php');
Application::import(PATH_APPLICATION . '/model/models/StaticSiteTextsModel.php');
Application::import(PATH_APPLICATION . '/model/models/ShopModel.php');
Application::import(PATH_APPLICATION . '/model/models/TicketsModel.php');


class Index extends \SlimController\SlimController 
{
    const NEWS_PER_PAGE = 6;
    const SHOP_PER_PAGE = 6;
    const LOTTERIES_PER_PAGE = 6;

    public $promoLang = '';

    public function indexAction()
    {
        $this->promoLang = Config::instance()->defaultLang;
        if (!Session::connect()->get(Player::IDENTITY)) {
            $this->landing();    
        } else {
            $this->game();
        }
        
    }

    protected function game()
    {
        Session::connect()->get(Player::IDENTITY)->fetch();

        $gameSettings          = GameSettingsModel::instance()->loadSettings();
        $lotteries             = LotteriesModel::instance()->getPublishedLotteriesList(self::LOTTERIES_PER_PAGE);
        $playerPlayedLotteries = LotteriesModel::instance()->getPlayerPlayedLotteries(Session::connect()->get(Player::IDENTITY)->getId(), self::LOTTERIES_PER_PAGE);

        $gameInfo = array(
            'participants' => PlayersModel::instance()->getPlayersCount(),
            'winners'      => 0,
            'win'          => 0,
            'nextLottery'  => $gameSettings->getNearestGame() + strtotime('00:00:00', time()) - time() + 10, // add 10 seconds to fill pause on lottery result generation
            'lotteryWins'  => $gameSettings->getPrizes($this->promoLang),
        );

        $staticTexts = $list = StaticSiteTextsModel::instance()->getListGroupedByIdentifier();
        $shop = ShopModel::instance()->loadShop();
        $news = NewsModel::instance()->getList($this->promoLang);

        $tickets = TicketsModel::instance()->getPlayerUnplayedTickets(Session::connect()->get(Player::IDENTITY));

        $this->render('production/game', array(
            'gameInfo'    => $gameInfo,
            'shop'        => $shop,
            'staticTexts' => $staticTexts,
            'lang'        => $this->promoLang,
            'currency'    => Config::instance()->langCurrencies[$this->promoLang],
            'news'        => $news,
            'player'      => Session::connect()->get(Player::IDENTITY),
            'tickets'     => $tickets,
            'layout'      => false,
            'lotteries'   => $lotteries,
            'playerPlayedLotteries' => $playerPlayedLotteries,
        ));
    }

    protected function landing()
    {
        $gameSettings = GameSettingsModel::instance()->loadSettings();

        $gameInfo = array(
            'participants' => PlayersModel::instance()->getPlayersCount(),
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
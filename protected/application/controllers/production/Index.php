<?php

namespace controllers\production;
use \GameSettingsModel, \StaticSiteTextsModel, \Application, \Config, \Player, \Session, \PlayersModel, \ShopModel, \NewsModel;
use \TicketsModel, \LotteriesModel, \SEOModel, \ChanceGamesModel, \GameSettings, \TransactionsModel, \CommentsModel, \EmailInvites, \Common;
use GeoIp2\Database\Reader;

Application::import(PATH_APPLICATION . '/model/models/GameSettingsModel.php');
Application::import(PATH_APPLICATION . '/model/models/StaticSiteTextsModel.php');
Application::import(PATH_APPLICATION . '/model/models/ShopModel.php');
Application::import(PATH_APPLICATION . '/model/models/TicketsModel.php');
Application::import(PATH_APPLICATION . '/model/models/ChanceGamesModel.php');


class Index extends \SlimController\SlimController 
{
    const NEWS_PER_PAGE = 6;
    const SHOP_PER_PAGE = 6;
    const LOTTERIES_PER_PAGE = 6;
    const TRANSACTIONS_PER_PAGE = 6;
    const COMMENTS_PER_PAGE = 8;

    public $promoLang = '';
    public $country = '';
    public $ref     = 0;

    public function indexAction()
    {
        // validate invite
        if ($hash = $this->request()->get('ivh')) {
            EmailInvites::instance()->getProcessor()->validateHash($hash);
        }
        $this->ref = $this->request()->get('ref', null);
        try {
            $geoReader =  new Reader(PATH_MMDB_FILE);
            $country = $geoReader->country(Common::getUserIp())->country;    
            $this->country = $country->isoCode;

            if (!in_array($country->isoCode, Config::instance()->langs)) {
                $country->isoCode = Config::instance()->defaultLang;
                $this->country = Config::instance()->defaultLang;
            }

            $this->promoLang  = Config::instance()->countryLangs[$country->isoCode];

        } catch (\Exception $e) {
            $this->country = Config::instance()->defaultLang;
            $this->promoLang = Config::instance()->countryLangs[Config::instance()->defaultLang];
        }

        if (!Session::connect()->get(Player::IDENTITY)) {
            $this->landing();    
        } else {
            $this->game();
            Session::connect()->get(Player::IDENTITY)->markOnline();
        }
        
    }

    protected function game()
    {
        $seo = SEOModel::instance()->getSEOSettings();
        Session::connect()->get(Player::IDENTITY)->fetch();

        $gameSettings          = GameSettingsModel::instance()->loadSettings();
        $lotteries             = LotteriesModel::instance()->getPublishedLotteriesList(self::LOTTERIES_PER_PAGE);
        $playerPlayedLotteries = LotteriesModel::instance()->getPlayerPlayedLotteries(Session::connect()->get(Player::IDENTITY)->getId(), self::LOTTERIES_PER_PAGE);
        $chanceGames           = ChanceGamesModel::instance()->getGamesSettings();
        $currentChanceGame     = Session::connect()->get('chanceGame');

        //if (!Session::connect()->get('MomentChanseLastDate') || time() - Session::connect()->get('MomentChanseLastDate') > $chanceGames['moment']->getMinTo() * 60) {
            Session::connect()->set('MomentChanseLastDate', time());
        //}
        $gameInfo = array(
            'participants' => PlayersModel::instance()->getPlayersCount(),
            'winners'      => LotteriesModel::instance()->getWinnersCount(),
            'win'          => LotteriesModel::instance()->getMoneyTotalWin()  * $gameSettings->getCountryCoefficient($this->country),
            'nextLottery'  => $gameSettings->getNearestGame() + strtotime('00:00:00', time()) - time(),
            'lotteryWins'  => $gameSettings->getPrizes($this->country),
        );

        $playerTransactions = array(
            GameSettings::CURRENCY_POINT => TransactionsModel::instance()->playerPointsHistory(Session::connect()->get(Player::IDENTITY)->getId(), self::TRANSACTIONS_PER_PAGE),
            GameSettings::CURRENCY_MONEY => TransactionsModel::instance()->playerMoneyHistory(Session::connect()->get(Player::IDENTITY)->getId(), self::TRANSACTIONS_PER_PAGE),
        );

        $staticTexts = $list = StaticSiteTextsModel::instance()->getListGroupedByIdentifier();
        $shop = ShopModel::instance()->loadShop();
        $news = NewsModel::instance()->getList($this->promoLang, self::NEWS_PER_PAGE);

        $tickets = TicketsModel::instance()->getPlayerUnplayedTickets(Session::connect()->get(Player::IDENTITY));

        $this->render('production/game', array(
            'gameInfo'    => $gameInfo,
            'country'     => $this->country,
            'shop'        => $shop,
            'staticTexts' => $staticTexts,
            'lang'        => $this->promoLang,
            'currency'    => Config::instance()->langCurrencies[$this->country],
            'news'        => $news,
            'player'      => Session::connect()->get(Player::IDENTITY),
            'tickets'     => $tickets,
            'layout'      => false,
            'lotteries'   => $lotteries,
            'playerPlayedLotteries' => $playerPlayedLotteries,
            'seo' => $seo,
            'chanceGames'  => $chanceGames,
            'currentChanceGame' => $currentChanceGame ? array_shift($currentChanceGame) : null,
            'playerTransactions' => $playerTransactions,
        ));
    }

    protected function landing()
    {
        $seo = SEOModel::instance()->getSEOSettings();
        $gameSettings = GameSettingsModel::instance()->loadSettings();
        $comments = CommentsModel::instance()->getList();

        $gameInfo = array(
            'participants' => PlayersModel::instance()->getPlayersCount(),
            'winners'      => LotteriesModel::instance()->getWinnersCount(),
            'win'          => LotteriesModel::instance()->getMoneyTotalWin() * $gameSettings->getCountryCoefficient($this->country),
            'nextLottery'  => $gameSettings->getNearestGame() + strtotime('00:00:00', time()) - time(),
            'lotteryWins'  => $gameSettings->getPrizes($this->country),
        );

        if (count($comments) > self::COMMENTS_PER_PAGE) {
            $ids = array_rand($comments,self::COMMENTS_PER_PAGE);
            $stripped = array();

            foreach ($ids as $id) {
                $stripped[] = $comments[$id];
            }
            $comments = $stripped;
        }
        $lastLottery = LotteriesModel::instance()->getPublishedLotteriesList(1);
        $lastLottery = array_shift($lastLottery);

        $staticTexts = $list = StaticSiteTextsModel::instance()->getListGroupedByIdentifier();        

        $this->render('production/landing', array(
            'gameInfo'    => $gameInfo,
            'country'     => $this->country,
            'staticTexts' => $staticTexts,
            'lang'        => $this->promoLang,
            'currency'    => Config::instance()->langCurrencies[$this->country],            
            'layout'      => false,
            'seo' => $seo,
            'comments'    => $comments,
            'lastLottery' => $lastLottery,
            'ref'         => $this->ref,
        ));
    }

    public function statsAction()
    {
       try {
            $geoReader =  new Reader(PATH_MMDB_FILE);
            $country = $geoReader->country(Common::getUserIp())->country;    
            $this->country = $country->isoCode;
            if (!in_array($country->isoCode, Config::instance()->langs)) {
                $country->isoCode = Config::instance()->defaultLang;
                $this->country = Config::instance()->defaultLang;
            }

            $this->promoLang  = Config::instance()->countryLangs[$country->isoCode];

        } catch (\Exception $e) {
            $this->country = Config::instance()->defaultLang;
            $this->promoLang = Config::instance()->countryLangs[Config::instance()->defaultLang];
        }
        $gameSettings = GameSettingsModel::instance()->loadSettings();
        if ($this->request()->isAjax()) {
            $info = array(
                'participants' => number_format(PlayersModel::instance()->getPlayersCount(), 0, '.', ' '),
                'winners'      => number_format(LotteriesModel::instance()->getWinnersCount(), 0, '.', ' '),                
                'win'          => number_format(LotteriesModel::instance()->getMoneyTotalWin()  * $gameSettings->getCountryCoefficient($this->country), 0, '.', ' ') . ' <span>' . Config::instance()->langCurrencies[$this->country] . '</span>',
            );

            die(json_encode(array('status' => 1, 'message' => 'OK', 'res' => $info)));
            
        }
        $this->redirect('/');
    }
}
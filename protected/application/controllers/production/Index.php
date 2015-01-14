<?php

namespace controllers\production;
use \GameSettingsModel, \StaticSiteTextsModel, \Application, \Config, \Player, \PlayersModel, \ShopModel, \NewsModel;
use \TicketsModel, \LotteriesModel, \SEOModel, \ChanceGamesModel, \GameSettings, \TransactionsModel, \NoticesModel, \ReviewsModel, \CommentsModel, \EmailInvites, \Common;
use GeoIp2\Database\Reader;
use Symfony\Component\HttpFoundation\Session\Session;

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
    const REVIEWS_PER_PAGE = 8;
    const COMMENTS_PER_PAGE = 9;

    const MONEY_ADD = 2070;
    const WINNERS_ADD = 29;

    public $promoLang = '';
    public $country = '';
    public $ref     = 0;

    public function indexAction()
    {

        $session = new Session();
        // validate registration
        if ($vh = $this->request()->get('vh')) {
            PlayersModel::instance()->validateHash($vh);
        }
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

        if (!$session->get(Player::IDENTITY)) {

            // check for autologin;
            if (!empty($_COOKIE[Player::AUTOLOGIN_COOKIE])) {
                $player = new Player();
                try {
                    if (!empty($_COOKIE[Player::AUTOLOGIN_HASH_COOKIE])) {
                        $player->setEmail($_COOKIE[Player::AUTOLOGIN_COOKIE])->fetch();

                        if ($player->generateAutologinHash() === $_COOKIE[Player::AUTOLOGIN_HASH_COOKIE]) {
                            $session->set(Player::IDENTITY, $player);
                            $player->markOnline();
                        }
                    }
                } catch (EntityException $e) {
                    // do nothing just show promo page
                }
            }
            $this->landing();    
        } else {
            // FORCE UPDATE POINTS AND MONEY FOR FIX WEBSOCKET SESSION
            try {
                $session->set(Player::IDENTITY, $session->get(Player::IDENTITY)->fetch());
                $this->country = (in_array($session->get(Player::IDENTITY)->getCountry(), Config::instance()->langs) ? $session->get(Player::IDENTITY)->getCountry() : Config::instance()->defaultLang );
                $this->game();
                $session->get(Player::IDENTITY)->markOnline();
            } catch (EntityException $e) {
                echo $e->getCode();
                if ($e->getCode() == 404) {
                    $session->remove(Player::IDENTITY);
                    $this->landing();
                }
            }
        }
        
    }

    protected function game()
    {
        $seo = SEOModel::instance()->getSEOSettings();
        $session = new Session();
        $session->get(Player::IDENTITY)->fetch();

        $banners          = Config::instance()->banners;
        $gameSettings          = GameSettingsModel::instance()->loadSettings();
        $lotteries             = LotteriesModel::instance()->getPublishedLotteriesList(self::LOTTERIES_PER_PAGE);
        $playerPlayedLotteries = LotteriesModel::instance()->getPlayerPlayedLotteries($session->get(Player::IDENTITY)->getId(), self::LOTTERIES_PER_PAGE);
        $chanceGames           = ChanceGamesModel::instance()->getGamesSettings();
        $currentChanceGame     = $_SESSION['chanceGame'];

        //if (!Session2::connect()->get('MomentChanseLastDate') || time() - Session2::connect()->get('MomentChanseLastDate') > $chanceGames['moment']->getMinTo() * 60) {
             $session->set('MomentChanseLastDate', time());
        //}
        $gameInfo = array(
            'participants' => PlayersModel::instance()->getPlayersCount(),
            'winners'      => LotteriesModel::instance()->getWinnersCount() + self::WINNERS_ADD,
            'win'          => (LotteriesModel::instance()->getMoneyTotalWin() + self::MONEY_ADD ) * $gameSettings->getCountryCoefficient($this->country),
            'nextLottery'  => $gameSettings->getNearestGame() + strtotime('00:00:00', time()) - time(),
            'lotteryWins'  => $gameSettings->getPrizes($this->country),
            'rate'         => $gameSettings->getCountryRate($this->country),
            'coefficient'  => $gameSettings->getCountryCoefficient($this->country),
        );

        $playerTransactions = array(
        //    GameSettings::CURRENCY_POINT => TransactionsModel::instance()->playerPointsHistory($session->get(Player::IDENTITY)->getId(), self::TRANSACTIONS_PER_PAGE),
        //    GameSettings::CURRENCY_MONEY => TransactionsModel::instance()->playerMoneyHistory($session->get(Player::IDENTITY)->getId(), self::TRANSACTIONS_PER_PAGE),
        );

        $staticTexts = $list = StaticSiteTextsModel::instance()->getListGroupedByIdentifier();
        $shop = ShopModel::instance()->loadShop();
        $news = NewsModel::instance()->getList($this->promoLang, self::NEWS_PER_PAGE);
        $reviews = ReviewsModel::instance()->getList(1, self::REVIEWS_PER_PAGE);
        $notices = NoticesModel::instance()->getPlayerUnreadNotices($session->get(Player::IDENTITY));

        $tickets = TicketsModel::instance()->getPlayerUnplayedTickets($session->get(Player::IDENTITY));
        $this->render('production/game_new', array(
            'gameInfo'    => $gameInfo,
            'country'     => $this->country,
            'shop'        => $shop,
            'staticTexts' => $staticTexts,
            'lang'        => $this->promoLang,
            'currency'    => Config::instance()->langCurrencies[$this->country],
            'notices'     => $notices,
            'news'        => $news,
            'reviews'     => $reviews,
            'player'      => $session->get(Player::IDENTITY),
            'tickets'     => $tickets,
            'layout'      => false,
            'lotteries'   => $lotteries,
            'playerPlayedLotteries' => $playerPlayedLotteries,
            'seo' => $seo,
            'chanceGames'  => $chanceGames,
            'currentChanceGame' => $currentChanceGame ? array_shift($currentChanceGame) : null,
            'playerTransactions' => $playerTransactions,
            'banners'      => $banners
        ));
    }

    protected function landing()
    {
        $showEmail = $this->request()->get('m', false);
        $showLoginScreen = false;
        $session = new Session();

        if (!empty($_COOKIE['showLoginScreen'])) {
            $showLoginScreen = true;
        }

        $seo = SEOModel::instance()->getSEOSettings();
        $gameSettings = GameSettingsModel::instance()->loadSettings();
        $comments = CommentsModel::instance()->getList();

        $gameInfo = array(
            'participants' => PlayersModel::instance()->getPlayersCount(),
            'winners'      => LotteriesModel::instance()->getWinnersCount() + self::WINNERS_ADD,
            'win'          => (LotteriesModel::instance()->getMoneyTotalWin() + self::MONEY_ADD) * $gameSettings->getCountryCoefficient($this->country),
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

        if($session->has('SOCIAL_IDENTITY'))
        {
            if($session->has('SOCIAL_IDENTITY_DISABLED')){
                $session->remove('SOCIAL_IDENTITY');
                $session->remove('SOCIAL_IDENTITY_DISABLED');
            }
            else{
                $socialIdentity = $session->get('SOCIAL_IDENTITY');
                $session->set('SOCIAL_IDENTITY_DISABLED',1);
            }
        }

        if($session->has('ERROR')){
            $error=$session->get('ERROR');
            $session->remove('ERROR');
        }


        $this->render('production/landing', array(
            'showLoginScreen' => $showLoginScreen,
            'showEmail'   => $showEmail,
            'gameInfo'    => $gameInfo,
            'socialIdentity'  => $socialIdentity,
            'country'     => $this->country,
            'error'       => $error,
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
                'participants' => Common::viewNumberFormat(PlayersModel::instance()->getPlayersCount()),
                'winners'      => Common::viewNumberFormat(LotteriesModel::instance()->getWinnersCount()  + self::WINNERS_ADD),
                'win'          => Common::viewNumberFormat(round(LotteriesModel::instance()->getMoneyTotalWin() + self::MONEY_ADD)) . ' <span>' . Config::instance()->langCurrencies[$this->country] . '</span>',
            );

            die(json_encode(array('status' => 1, 'message' => 'OK', 'res' => $info)));
            
        }
        $this->redirect('/');
    }

    public function VKProxyAction() 
    {
        $upload_url = $this->request()->post('uurl');
        $post_params['photo'] = '@' . PATH_ROOT . 'tpl/img/social-share.jpg'; ///ну тут понятно что это ваша фотка
     
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $upload_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
        $result = curl_exec($ch);
        curl_close($ch);
       
        die(json_encode(array('status' => 1, 'message' => 'OK', 'res' => json_decode($result))));
    }

    public function feedbackAction()
    {
        $response = array(
            'status'  => 1,
            'message' => 'OK',
            'res'     => array(),
        );

        $email = $this->request()->post('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['status'] = 0;
            $response['message'] = 'INVALID_EMAIL';

            die(json_encode($response));
        }
        $text = $this->request()->post('text');
        if (empty($text)) {
            $response['status'] = 0;
            $response['message'] = 'EMPTY_TEXT';

            die(json_encode($response));
        }
        $text = htmlspecialchars(strip_tags($text));

        Common::sendEmail('partners@lotozon.com', 'Вопрос от ' . $email, 'feedback', array(
            'email'  => $email,
            'text' => $text,
        ));

        die(json_encode($response));
    }
}
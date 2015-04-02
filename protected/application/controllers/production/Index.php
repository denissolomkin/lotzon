<?php

namespace controllers\production;
use \OnlineGamesModel, \LotterySettingsModel, \StaticSiteTextsModel, \Application, \Config, \Player, \PlayersModel, \ShopModel, \NewsModel;
use \TicketsModel, \LotteriesModel, \CountriesModel, \SEOModel, \ChanceGamesModel, \GameSettingsModel, \QuickGamesModel, \LotterySettings, \TransactionsModel, \NoticesModel, \ReviewsModel, \CommentsModel, \EmailInvites, \Common;
use GeoIp2\Database\Reader;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . '/model/models/LotterySettingsModel.php');
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

    public $lang = '';
    public $country = '';
    public $ref     = 0;

    public function indexAction($page='tickets')
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
            $country = $geoReader->country(Common::getUserIp())->country->isoCode;

            if (!CountriesModel::instance()->isCountry($country)){
                $this->country = CountriesModel::instance()->defaultCountry();
                $this->lang  = CountriesModel::instance()->defaultLang();
            } else {
                $this->country = $country;
                $this->lang = CountriesModel::instance()->getCountry($country)->getLang();
            }

        } catch (\Exception $e) {
            $this->country = CountriesModel::instance()->defaultCountry();
            $this->lang = CountriesModel::instance()->defaultLang();
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

                $this->country = (
                CountriesModel::instance()->isCountry($session->get(Player::IDENTITY)->getCountry())
                    ? $session->get(Player::IDENTITY)->getCountry()
                    : CountriesModel::instance()->defaultCountry());

                $this->lang = (
                CountriesModel::instance()->isLang($session->get(Player::IDENTITY)->getLang())
                    ? $session->get(Player::IDENTITY)->getLang()
                    : CountriesModel::instance()->defaultLang());

                $this->game($page);
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

    protected function game($page)
    {

        $seo                   = SEOModel::instance()->getSEOSettings();
        $seo['page']           = ($seo['pages']?$page:0);
        $session               = new Session();
        $player                = $session->get(Player::IDENTITY)->fetch();
        $banners               = Config::instance()->banners;
        $lotterySettings       = LotterySettingsModel::instance()->loadSettings();
        $lotteries             = LotteriesModel::instance()->getPublishedLotteriesList(self::LOTTERIES_PER_PAGE);
        $playerPlayedLotteries = LotteriesModel::instance()->getPlayerPlayedLotteries($player->getId(), self::LOTTERIES_PER_PAGE);
        $onlineGames           = OnlineGamesModel::instance()->getList();
        $quickGames            = QuickGamesModel::instance()->getList();
        $gameSettings          = GameSettingsModel::instance()->getList();
        $langs                 = $seo['multilanguage'] ? CountriesModel::instance()->getLangs() : null;

        \StaticTextsModel::instance()->setLang($this->lang);

        if (!$session->has('MomentLastDate'))
            $session->set('MomentLastDate', time());

        if (!$session->has('QuickGameLastDate'))
            $session->set('QuickGameLastDate',time());

        $gameInfo = array(
            'participants' => PlayersModel::instance()->getMaxId(),
            'winners'      => LotteriesModel::instance()->getWinnersCount() + self::WINNERS_ADD,
            'win'          => (LotteriesModel::instance()->getMoneyTotalWin() + self::MONEY_ADD ) * CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getCoefficient(),
            'nextLottery'  => $lotterySettings->getNearestGame() + strtotime('00:00:00', time()) - time(),
            'lotteryWins'  => $lotterySettings->getPrizes($this->country),
        );

        $playerTransactions = array(
        //    LotterySettings::CURRENCY_POINT => TransactionsModel::instance()->playerPointsHistory($session->get(Player::IDENTITY)->getId(), self::TRANSACTIONS_PER_PAGE),
        //    LotterySettings::CURRENCY_MONEY => TransactionsModel::instance()->playerMoneyHistory($session->get(Player::IDENTITY)->getId(), self::TRANSACTIONS_PER_PAGE),
        );

        if(is_array(Config::instance()->blockedReferers) && parse_url($_SERVER['HTTP_REFERER'])['host'] && in_array(parse_url($_SERVER['HTTP_REFERER'])['host'], Config::instance()->blockedReferers) && !$session->has('REFERER'))
            $session->set('REFERER',parse_url($_SERVER['HTTP_REFERER'])['host']);

        $staticTexts = $list = StaticSiteTextsModel::instance()->getListGroupedByIdentifier();
        $shop = ShopModel::instance()->loadShop();
        $news = array(); //$news = NewsModel::instance()->getList($this->lang, self::NEWS_PER_PAGE);
        $reviews = ReviewsModel::instance()->getList(1, self::REVIEWS_PER_PAGE);
        $notices = NoticesModel::instance()->getPlayerUnreadNotices($player);
        $tickets = TicketsModel::instance()->getPlayerUnplayedTickets($player);
        $this->render('production/game_new', array(
            'gameInfo'    => $gameInfo,
            'country'     => $this->country,
            'shop'        => $shop,
            'staticTexts' => $staticTexts,
            'lang'        => $this->lang,
            'currency'    => CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getSettings(),
            'notices'     => $notices,
            'news'        => $news,
            'reviews'     => $reviews,
            'player'      => $player,
            'tickets'     => $tickets,
            'layout'      => false,
            'lotteries'   => $lotteries,
            'playerPlayedLotteries' => $playerPlayedLotteries,
            'seo'         => $seo,
            'onlineGames' => $onlineGames,
            'langs'       => $langs,
            'quickGames'  => $quickGames,
            'gameSettings'=> $gameSettings,
            'chanceGame'  => $session->has('ChanceGame') ? $session->get('ChanceGame')->getId() : null,
            'quickGame'   => array(
                'current' => $session->has('QuickGame'),
                'title'   => $gameSettings['QuickGame']->getTitle(),
                'timer'   => $session->get('QuickGameLastDate') +  $gameSettings['QuickGame']->getOption('min')  * 60 - time()),
            'playerTransactions' => $playerTransactions,
            'banners'     => $banners
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
        $lotterySettings = LotterySettingsModel::instance()->loadSettings();
        $comments = CommentsModel::instance()->getList();

        $gameInfo = array(
            'participants' => PlayersModel::instance()->getMaxId(),
            'winners'      => LotteriesModel::instance()->getWinnersCount() + self::WINNERS_ADD,
            'win'          => (LotteriesModel::instance()->getMoneyTotalWin() + self::MONEY_ADD) * CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getCoefficient(),
            'nextLottery'  => $lotterySettings->getNearestGame() + strtotime('00:00:00', time()) - time(),
            'lotteryWins'  => $lotterySettings->getPrizes($this->country),
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

        if($session->has('ERROR') OR $_SESSION['ERROR']){
            $error=$session->get('ERROR')?:$_SESSION['ERROR'];
            $session->remove('ERROR');unset($_SESSION['ERROR']);
        }

        $referer=parse_url($_SERVER['HTTP_REFERER']);
        if($referer && is_array(Config::instance()->blockedReferers) && !$session->has('REFERER') && ( ($referer['host'] && in_array(str_replace('www','',$referer['host']), Config::instance()->blockedReferers)) OR ($referer['path'] && in_array(str_replace('www','',$referer['path']), Config::instance()->blockedReferers)))){
            $session->set('REFERER',$referer['host']?:$referer['path']);
        }
        $this->render('production/landing', array(
            'showLoginScreen' => $showLoginScreen,
            'showEmail'   => $showEmail,
            'gameInfo'    => $gameInfo,
            'socialIdentity'  => $socialIdentity,
            'country'     => $this->country,
            'error'       => $error,
            'staticTexts' => $staticTexts,
            'lang'        => $this->lang,
            'partners'    => Config::instance()->partners,
            'currency'    => CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getTitle('iso'),
            'layout'      => false,
            'seo'         => $seo,
            'rules'       => (bool) stristr($_SERVER['REQUEST_URI'],'rules'),
            'comments'    => $comments,
            'lastLottery' => $lastLottery,
            'ref'         => $this->ref,
            'metrikaDisabled' => $session->get('REFERER'),
        ));
    }

    public function statsAction()
    {

        try {
            $geoReader =  new Reader(PATH_MMDB_FILE);
            $country = $geoReader->country(Common::getUserIp())->country->isoCode;

            if (!CountriesModel::instance()->isCountry($country)){
                $this->country = CountriesModel::instance()->defaultCountry();
                $this->lang  = CountriesModel::instance()->defaultLang();
            } else {
                $this->country = $country;
                $this->lang = CountriesModel::instance()->getCountry($country)->getLang();
            }

        } catch (\Exception $e) {
            $this->country = CountriesModel::instance()->defaultCountry();
            $this->lang = CountriesModel::instance()->defaultLang();
        }

        // $lotterySettings = LotterySettingsModel::instance()->loadSettings();
        if ($this->request()->isAjax()) {
            $info = array(
                'participants' => Common::viewNumberFormat(PlayersModel::instance()->getMaxId()),
                'winners'      => Common::viewNumberFormat(LotteriesModel::instance()->getWinnersCount()  + self::WINNERS_ADD),
                'win'          => Common::viewNumberFormat(round(LotteriesModel::instance()->getMoneyTotalWin() + self::MONEY_ADD)) . ' <span>' .
                    CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getTitle('iso') . '</span>',
            );

            die(json_encode(array('status' => 1, 'message' => 'OK', 'res' => $info)));
            
        }
        $this->redirect('/');
    }

    public function VKProxyAction() 
    {
        $upload_url = $this->request()->post('uurl');

        // if use php<=5.5.0
        if (!function_exists('curl_file_create')) {
            $post_params['photo'] = "@".PATH_ROOT . 'tpl/img/social-share.jpg'.";filename=".basename(PATH_ROOT . 'tpl/img/social-share.jpg');
        }
        else {
            $post_params['photo'] = curl_file_create(PATH_ROOT . 'tpl/img/social-share.jpg');
        }
     
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
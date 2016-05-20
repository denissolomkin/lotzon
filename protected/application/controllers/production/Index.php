<?php

namespace controllers\production;

use \LotterySettingsModel, \SettingsModel, \StaticTextsModel, \Player, \PlayersModel;
use \TicketsModel, \LotteriesModel, \CountriesModel, \SEOModel, \LanguagesModel, \CommentsModel, \EmailInvites, \Common;
use GeoIp2\Database\Reader;
use Symfony\Component\HttpFoundation\Session\Session;
use \Detection\MobileDetect;

require_once(PATH_ROOT . 'vendor/mobiledetect/mobiledetectlib/namespaced/Detection/MobileDetect.php');

class Index extends \SlimController\SlimController
{

    public $lang    = '';
    public $country = '';
    public $currency = '';
    public $ref     = 0;
    protected $session;

    public function __construct(\Slim\Slim &$app)
    {
        parent::__construct($app);
        $this->init();
    }

    public function init()
    {
        $this->session = new Session();
    }

    public function finishRegistration($vh, $m)
    {
        $loggedIn = false;

        // create player
        $player = new Player();
        $player->setEmail($m);
        try {
            $player->loadPreregistration();
            if ($player->getHash() != $vh) {
                throw new \ModelException("Player not found", 404);
            }
            if ($player->getSocialName() != '') {
                $player->isSocialUsed();
            }
            try {
                $geoReader = new Reader(PATH_MMDB_FILE);
                $country   = $geoReader->country(Common::getUserIp())->country;
                $player->setCountry($country->isoCode);
            } catch (\Exception $e) {
                $player->setCountry(CountriesModel::instance()->defaultCountry());
            }

            $currency = CountriesModel::instance()->isCountry($player->getCountry())
                ? $player->getCountry()
                : CountriesModel::instance()->defaultCountry();

            $player->setLang(CountriesModel::instance()->getCountry($player->getCountry())->getLang())
                ->setCurrency($currency);

            $player->setValid(true)
                ->setDates(time(), 'Login')
                ->setComplete(false)//todo: remove when set default=false in database
                ->create()
                ->deletePreregistration();

            if ($player->getSocialId()) {
                $player->updateSocial();
                if (SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_registration')) {
                    $player->addPoints(
                        SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_registration'),
                        StaticTextsModel::instance()->setLang($player->getLang())->getText('bonus_social_registration') . $player->getSocialName());
                }
            }
            if (SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_registration')) {
                $player->addPoints(
                    SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_registration'),
                    StaticTextsModel::instance()->setLang($player->getLang())->getText('bonus_registration'));
            }

            $player->payInvite()
                ->payReferal()
                ->markOnline();
            $loggedIn = true;
        } catch (\ModelException $e) {
            try {
                $player->fetch();
                if ($player->getHash() == $vh) {
                    $player->setValid(true)
                        ->update()
                        ->setDates(time(), 'Login')
                        ->setComplete(false)//todo: remove when set default=false in database
                        ->payInvite()
                        ->payReferal()
                        ->markOnline();
                    $loggedIn = true;
                }
            } catch (\EntityException $e) {

            }
        }

        if ($loggedIn === true) {
            $this->session->set(Player::IDENTITY, $player);
            $player->setLastIp(Common::getUserIp())
                ->setAgent($_SERVER['HTTP_USER_AGENT'])
                ->writeLogin();
        }

        $this->redirect(strstr($_SERVER['HTTP_REFERER'], 'lotzon.com') ? $_SERVER['HTTP_REFERER'] : '/');
    }


    public function indexAction($page = 'home')
    {
        // validate registration
        if ($vh = $this->request()->get('vh')) {
            $m = $this->request()->get('m');
            $this->finishRegistration($vh, $m);
        }
        // validate invite
        if ($hash = $this->request()->get('ivh')) {
            EmailInvites::instance()->getProcessor()->validateHash($hash);
        }
        $this->ref = $this->request()->get('ref', null);
        if ($this->ref!==null) {
            setcookie ("ref", $this->ref, time() + 2592000);
        }

        if ((!empty($_COOKIE['ref']))&($this->ref===null)) {
            $this->ref = $_COOKIE['ref'];
        }

        try {
            $geoReader = new Reader(PATH_MMDB_FILE);
            $country   = $geoReader->country(Common::getUserIp())->country->isoCode;

            if (!CountriesModel::instance()->isCountry($country)) {
                $this->country = CountriesModel::instance()->defaultCountry();
                $this->lang    = CountriesModel::instance()->defaultLang();
            } else {
                $this->country = $country;
                $this->lang    = CountriesModel::instance()->getCountry($country)->getLang();
            }

        } catch (\Exception $e) {
            $this->country = CountriesModel::instance()->defaultCountry();
            $this->lang    = CountriesModel::instance()->defaultLang();
        }
        $this->currency = $this->country;

        if (!$this->session->get(Player::IDENTITY)) {

            if ($this->request()->get('guest')) {
                $this->game_noauth();
                return true;
            }
            // check for autologin;
            if (!empty($_COOKIE[Player::AUTOLOGIN_COOKIE])) {
                $player = new Player();
                try {
                    if (!empty($_COOKIE[Player::AUTOLOGIN_HASH_COOKIE])) {
                        $player->setEmail($_COOKIE[Player::AUTOLOGIN_COOKIE])->fetch();

                        if ($player->generateAutologinHash() === $_COOKIE[Player::AUTOLOGIN_HASH_COOKIE]) {
                            $this->session->set(Player::IDENTITY, $player);
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
                $this->session->set(Player::IDENTITY, $this->session->get(Player::IDENTITY)->fetch());

                $this->country = (
                CountriesModel::instance()->isCountry($this->session->get(Player::IDENTITY)->getCountry())
                    ? $this->session->get(Player::IDENTITY)->getCountry()
                    : CountriesModel::instance()->defaultCountry());

                $this->lang = (
                LanguagesModel::instance()->isLang($this->session->get(Player::IDENTITY)->getLang())
                    ? $this->session->get(Player::IDENTITY)->getLang()
                    : CountriesModel::instance()->defaultLang());

                $this->currency = (
                CountriesModel::instance()->isCountry($this->session->get(Player::IDENTITY)->getCurrency())
                    ? $this->session->get(Player::IDENTITY)->getCurrency()
                    : CountriesModel::instance()->defaultCountry());

                $this->game($page);
                $this->session->get(Player::IDENTITY)->markOnline();
            } catch (EntityException $e) {
                echo $e->getCode();
                if ($e->getCode() == 404) {
                    $this->session->remove(Player::IDENTITY);
                    $this->landing();
                }
            }
        }

    }

    protected function game_noauth()
    {
        $detect   = new MobileDetect;
        if ($detect->version('IE')!==false) {
            $this->render('../../res/browser_error.php', array('layout' => false,));
            return false;
        }

        $isMobile = $detect->isMobile();
        $counters = \SettingsModel::instance()->getSettings('counters');
        $seo      = SEOModel::instance()->getSEOSettings();

        $country = Common::getUserIpCountry();

        $currency = CountriesModel::instance()->isCountry($country)
            ? $country
            : CountriesModel::instance()->defaultCountry();

        $this->session->set('isMobile', $isMobile);

        if(($page = $this->session->get('page'))) {
            $this->session->remove('page');
        }

        $config = array(
            'unauthorized' => true,
            'timeout'            => array(
                'ping'   => (int)$counters->getValue('PLAYER_TIMEOUT'),
                'online' => (int)$counters->getValue('PLAYER_TIMEOUT')
            ),
            'adminId'            => (int)$counters->getValue('USER_REVIEW_DEFAULT'),
            'minMoneyOutput'          => (int)$counters->getValue('MIN_MONEY_OUTPUT'),
            'tempFilestorage'    => '/filestorage/temp',
            'filestorage'        => '/filestorage',
            'websocketUrl'       => 'ws' . (\Config::instance()->SSLEnabled ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . ':' . \Config::instance()->wsPort,
            'websocketEmulation' => false,
            'page'               => $page,
            'limits'             => array(
                'lottery-history' => (int)$counters->getValue('LOTTERIES_PER_PAGE'),
                'communication-comments' => (int)$counters->getValue('COMMENTS_PER_PAGE'),
                'communication-messages' => (int)$counters->getValue('MESSAGES_PER_PAGE'),
                'communication-notifications' => (int)$counters->getValue('NOTIFICATIONS_PER_PAGE'),
                'users-friends' => (int)$counters->getValue('FRIENDS_PER_PAGE'),
                'blog-posts' => (int)$counters->getValue('POSTS_PER_PAGE'),
            ),
            'yandexMetrika' => (int)$counters->getValue('YANDEX_METRIKA'),
            'googleAnalytics' => $counters->getValue('GOOGLE_ANALYTICS'),
            'captchaKey' => $counters->getValue('CAPTCHA_CLIENT'),
            'siteVersion' => $seo['SiteVersion'],
        );

        $debug = array(
            'config' => array(
                'dev'     => \Config::instance()->dev ?: false,
                'stat'    => false,
                'alert'   => false,
                'render'  => false,
                'cache'   => false,
                'i18n'    => false,
                'func'    => true,
                'info'    => true,
                'warn'    => true,
                'error'   => true,
                'log'     => true,
                'clean'   => true,
                'content' => true
            )
        );

        $lottery = LotteriesModel::instance()->getPublishedLotteriesList(1);
        $lottery = array_shift($lottery);

        $playerObj = array(
            'is' => array(
                'complete' => true,
            ),
            'language' => array(
                //'current'   => $country,
                'current'   => 'RU',
                'available' => array(
                    'RU' => 'Русский',
                    'EN' => 'English',
                    'UA' => 'Украiнська'
                )
            ),
            'currency' => $currency,
        );

        $lottery = array(
            'lastLotteryId'    => $lottery->getId(),
            'timeToLottery'    => LotterySettingsModel::instance()->loadSettings()->getNearestGame() + strtotime('00:00:00', time()) - time(),
            'selectedTab'      => null,
            'ticketConditions' => array(
                4 => (int)\SettingsModel::instance()->getSettings('ticketConditions')->getValue('CONDITION_4_TICKET'),
                5 => (int)\SettingsModel::instance()->getSettings('ticketConditions')->getValue('CONDITION_5_TICKET'),
                6 => (int)\SettingsModel::instance()->getSettings('ticketConditions')->getValue('CONDITION_6_TICKET'),
            ),
            'totalBalls'       => \LotterySettings::TOTAL_BALLS,
            'requiredBalls'    => \LotterySettings::REQUIRED_BALLS,
            'totalTickets'     => \LotterySettings::TOTAL_TICKETS,
            'filledTickets'    => array(1=>null,2=>null,3=>null,4=>false,5=>false,6=>false,7=>false,8=>false),
            'priceGold'        => SettingsModel::instance()->getSettings('goldPrice')->getValue($currency),
            'priceGoldTicket'  => array(
                'money'  => SettingsModel::instance()->getSettings('goldPrice')->getValue($currency),
                'points' => SettingsModel::instance()->getSettings('goldPrice')->getValue('POINTS'),
            ),
            'prizes'           => array(
                'default' => LotterySettingsModel::instance()->loadSettings()->getPrizes($currency),
                'gold'    => LotterySettingsModel::instance()->loadSettings()->getGoldPrizes($currency),
            ),
        );

        $this->render('../../res/index.php', array(
            'layout'    => false,
            'player'    => $playerObj,
            'lottery'   => $lottery,
            'debug'     => $debug,
            'slider'    => array(),
            'config'    => $config,
            'isMobile'  => $isMobile,
            'seo'       => $seo,
            'version'   => $seo['SiteVersion']
        ));
    }

    protected function game($page)
    {
        $detect   = new MobileDetect;
        if ($detect->version('IE')!==false) {
            $this->render('../../res/browser_error.php', array('layout' => false,));
            return false;
        }

        $isMobile = $detect->isMobile();
        $counters = \SettingsModel::instance()->getSettings('counters');
        $seo      = SEOModel::instance()->getSEOSettings();

        $this->session->set('isMobile', $isMobile);
        $player = $this->session->get(Player::IDENTITY)->fetch();

        $gamePlayer = new \GamePlayer();
        $gamePlayer->setId($player->getId())->fetch();

        if(($error = ($this->session->get('ERROR') ?: ($_SESSION['ERROR'] ?: false)))) {
            $this->session->remove('ERROR');
            unset($_SESSION['ERROR']);
        }

        if(($page = $this->session->get('page'))) {
            $this->session->remove('page');
        }

        /* todo delete
        patch for old Player Entity in Memcache sessions
        */
        
        try {

            if($this->session->get(Player::IDENTITY)->getVersion() !== 3)
                throw(new \Exception);

        } catch (\Exception $e) {
            $this->session->get(Player::IDENTITY)->fetch();
            $playerId = $player->getId();
            $player = new Player();
            $player
                ->setId($playerId)
                ->fetch()
                ->initDates()
                ->initPrivacy()
                ->initCounters()
                ->initAccounts();
            $this->session->set(Player::IDENTITY, $player);
        }


        $playerObj = array(
            'id'       => $player->getId(),
            'img'      => $player->getAvatar(),
            'email'    => $player->getEmail(),
            'gender'   => $player->getGender(),
            'is' => array(
                'complete' => $player->isComplete(),
                'valid'    => $player->isValid(),
                'moderator'=> in_array($player->getId(), (array) SettingsModel::instance()->getSettings('moderators')->getValue()),
            ),
            'dates'    => array(
                'registration' => $player->getDates('Registration')
            ),
            'privacy'  => $player->getPrivacy(),
            'title'    => array(
                'name'       => $player->getName(),
                'surname'    => $player->getSurname(),
                'patronymic' => $player->getSecondName(),
                'nickname'   => $player->getNicname(),
            ),
            'language' => array(
                'current'   => $player->getLang(),
                'available' => array(
                    'RU' => 'Русский',
                    'EN' => 'English',
                    'UA' => 'Украiнська'
                )
            ),
            'birthday' => $player->getBirthday(),
            'count'    => array(
                'lotteries'     => $player->getGamesPlayed(),
                'friends' => \FriendsModel::instance()->getStatusCount($player->getId(), 1),
                'menu' => array(
                    'users' => array(
                        'requests' => \FriendsModel::instance()->getStatusCount($player->getId(), 0, true),
                    ),
                    'communication' => array(
                        'notifications' => array(
                            'server' => \CommentsModel::instance()->getNotificationsCount($player->getId()),
                            'local' => 0
                        ),
                        'messages'      => \MessagesModel::instance()->getStatusCount($player->getId(), 0)
                    )
                ),
            ),
            'favorite' => $player->getFavoriteCombination(),
            'location' => array(
                'country' => $player->getCountry(),
                'city'    => $player->getCity(),
                'zip'     => $player->getZip(),
                'address' => $player->getAddress(),
            ),
            'balance'  => array(
                'points' => $player->getPoints(),
                'money'  => $player->getMoney(),
                'lotzon' => 1500
            ),
            'currency' => CountriesModel::instance()->getCountry($this->currency)->loadCurrency()->getSettings(),
            'billing'  => array(
                'webmoney'      => $player->getAccounts('WebMoney') ? $player->getAccounts('WebMoney')[0] : null,
                'yandex'        => $player->getAccounts('YandexMoney') ? $player->getAccounts('YandexMoney')[0] : null,
                'qiwi'          => $player->getAccounts('Qiwi') ? $player->getAccounts('Qiwi')[0] : null,
                'phone'         => $player->getAccounts('Phone') ? $player->getAccounts('Phone')[0] : null,
            ),
            'accounts' => $player->getAccounts(),
            'social'   => $player->getSocial(),
            'settings' => array(
                'newsSubscribe' => $player->getNewsSubscribe()
            ),
            'games'    => array(
                'chance' => $this->session->has('ChanceGame') ? $this->session->get('ChanceGame')->getId() : false,
                'random' => $this->session->has('QuickGame'),
                'moment' => $this->session->has('Moment'),
                'online' => $gamePlayer->getApp() ?: array(
                    'Key' => $gamePlayer->getApp('Key'),
                    'Uid' => $gamePlayer->getApp('Uid')
                ),
            ),
            'referral' => array(
                'total'  => PlayersModel::instance()->getReferralsCount($player->getId()),
                'profit' => $player->getReferralsProfit()
            )
        );

        $lottery = LotteriesModel::instance()->getPublishedLotteriesList(1);
        $lottery = array_shift($lottery);

        /* todo delete slider */
        $slider = array(
            'sum'     => (LotteriesModel::instance()->getMoneyTotalWin() + $counters->getValue('MONEY_ADD')) * CountriesModel::instance()->getCountry($this->currency)->loadCurrency()->getCoefficient(),
            'winners' => LotteriesModel::instance()->getWinnersCount() + $counters->getValue('WINNERS_ADD'),
            'jackpot' => LotterySettingsModel::instance()->loadSettings()->getPrizes($this->currency)[6]['sum'],
            'players' => PlayersModel::instance()->getMaxId(),
            'timer'   => LotterySettingsModel::instance()->loadSettings()->getNearestGame() + strtotime('00:00:00', time()) - time(),
            'lottery' => array(
                'id'    => $lottery->getId(),
                'date'  => $lottery->getDate(),
                'balls' => $lottery->getCombination()
            )
        );

        $config = array(
            'timeout'            => array(
                'ping'   => (int)$counters->getValue('PLAYER_TIMEOUT'),
                'online' => (int)$counters->getValue('PLAYER_TIMEOUT')
            ),
            'adminId'            => (int)$counters->getValue('USER_REVIEW_DEFAULT'),
            'minMoneyOutput'          => (int)$counters->getValue('MIN_MONEY_OUTPUT'),
            'tempFilestorage'    => '/filestorage/temp',
            'filestorage'        => '/filestorage',
            'websocketUrl'       => 'ws' . (\Config::instance()->SSLEnabled ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . ':' . \Config::instance()->wsPort,
            'websocketEmulation' => false,
            'page'               => $page,
            'limits'             => array(
                'lottery-history' => (int)$counters->getValue('LOTTERIES_PER_PAGE'),
                'communication-comments' => (int)$counters->getValue('COMMENTS_PER_PAGE'),
                'communication-messages' => (int)$counters->getValue('MESSAGES_PER_PAGE'),
                'communication-notifications' => (int)$counters->getValue('NOTIFICATIONS_PER_PAGE'),
                'users-friends' => (int)$counters->getValue('FRIENDS_PER_PAGE'),
                'blog-posts' => (int)$counters->getValue('POSTS_PER_PAGE'),
            ),
            'yandexMetrika' => (int)$counters->getValue('YANDEX_METRIKA'),
            'googleAnalytics' => $counters->getValue('GOOGLE_ANALYTICS'),
            'captchaKey' => $counters->getValue('CAPTCHA_CLIENT'),
            'siteVersion' => $seo['SiteVersion'],
        );

        $debug = array(
            'config' => array(
                'dev'     => \Config::instance()->dev ?: false,
                'stat'    => false,
                'alert'   => false,
                'render'  => false,
                'cache'   => false,
                'i18n'    => false,
                'func'    => true,
                'info'    => true,
                'warn'    => true,
                'error'   => true,
                'log'     => true,
                'clean'   => true,
                'content' => true
            )
        );

        $lottery = array(
            'lastLotteryId'    => $lottery->getId(),
            'timeToLottery'    => LotterySettingsModel::instance()->loadSettings()->getNearestGame() + strtotime('00:00:00', time()) - time(),
            'selectedTab'      => null,
            'ticketConditions' => array(
                4 => (int)\SettingsModel::instance()->getSettings('ticketConditions')->getValue('CONDITION_4_TICKET'),
                5 => (int)\SettingsModel::instance()->getSettings('ticketConditions')->getValue('CONDITION_5_TICKET'),
                6 => (int)\SettingsModel::instance()->getSettings('ticketConditions')->getValue('CONDITION_6_TICKET'),
            ),
            'totalBalls'       => \LotterySettings::TOTAL_BALLS,
            'requiredBalls'    => \LotterySettings::REQUIRED_BALLS,
            'totalTickets'     => \LotterySettings::TOTAL_TICKETS,
            'filledTickets'    => \TicketsModel::instance()->getUnplayedTickets($player->getId()),
            'priceGold'        => SettingsModel::instance()->getSettings('goldPrice')->getValue($this->currency),
            'priceGoldTicket'  => array(
                'money'  => SettingsModel::instance()->getSettings('goldPrice')->getValue($this->currency),
                'points' => SettingsModel::instance()->getSettings('goldPrice')->getValue('POINTS'),
            ),
            'prizes'           => array(
                'default' => LotterySettingsModel::instance()->loadSettings()->getPrizes($this->currency),
                'gold'    => LotterySettingsModel::instance()->loadSettings()->getGoldPrizes($this->currency),
            ),
        );

        /*
        if (!$this->session->has('MomentLastDate'))
            $this->session->set('MomentLastDate', time());
        */

        if (!$this->session->has('QuickGameLastDate'))
            $this->session->set('QuickGameLastDate', time());

        $blockedReferers = SettingsModel::instance()->getSettings('blockedReferers')->getValue();
        if (is_array($blockedReferers) && parse_url($_SERVER['HTTP_REFERER'])['host'] && in_array(parse_url($_SERVER['HTTP_REFERER'])['host'], $blockedReferers) && !$this->session->has('REFERER'))
            $this->session->set('REFERER', parse_url($_SERVER['HTTP_REFERER'])['host']);

        $this->render('../../res/index.php', array(
            'layout'    => false,
            'player'    => $playerObj,
            'lottery'   => $lottery,
            'debug'     => $debug,
            'slider'    => $slider,
            'config'    => $config,
            'isMobile'  => $isMobile,
            'seo'       => $seo,
            'version'   => $seo['SiteVersion']
        ));

    }

    protected function landing()
    {

        $detect   = new MobileDetect;

        $slider = array(
            "sum"     => round((LotteriesModel::instance()->getMoneyTotalWin() + SettingsModel::instance()->getSettings('counters')->getValue('MONEY_ADD')) * CountriesModel::instance()->getCountry($this->currency)->loadCurrency()->getCoefficient()),
            "players" => PlayersModel::instance()->getMaxId(),
            "jackpot" => LotterySettingsModel::instance()->loadSettings()->getGoldPrizes($this->currency)[6]['sum']
        );

        $player = array(
            "language" => array(
                "current"   => $this->lang,
                "available" => array(
                    "RU" => "Русский",
                    "EN" => "English",
                    "UA" => "Украiнська"
                )
            ),
            "location" => array(
                "country" => $this->country
            ),
            "currency" => CountriesModel::instance()->getCountry($this->currency)->loadCurrency()->getSettings()
        );

        $error = array();
        if ($this->session->has('ERROR') OR $_SESSION['ERROR']) {
            $error['message'] = $this->session->get('ERROR') ?: $_SESSION['ERROR'];
            $this->session->remove('ERROR');
            unset($_SESSION['ERROR']);
        }
        if ($this->session->has('ERROR_CODE') OR $_SESSION['ERROR_CODE']) {
            $error['code'] = $this->session->get('ERROR_CODE') ?: $_SESSION['ERROR_CODE'];
            $this->session->remove('ERROR_CODE');
            unset($_SESSION['ERROR_CODE']);
        }

        $referer         = parse_url($_SERVER['HTTP_REFERER']);
        $blockedReferers = SettingsModel::instance()->getSettings('blockedReferers')->getValue();
        if ($referer && is_array($blockedReferers) && !$this->session->has('REFERER')
            && (($referer['host'] && in_array(str_replace('www', '', $referer['host']), $blockedReferers)) OR ($referer['path'] && in_array(str_replace('www', '', $referer['path']), $blockedReferers)))
        ) {
            $this->session->set('REFERER', $referer['host'] ?: $referer['path']);
        }

        $metrika = array(
            'metrikaDisabled' => $this->session->get('REFERER'),
            'yandexMetrika' => (int)SettingsModel::instance()->getSettings('counters')->getValue('YANDEX_METRIKA'),
            'googleAnalytics' => SettingsModel::instance()->getSettings('counters')->getValue('GOOGLE_ANALYTICS')
        );

        if ($this->session->has('SOCIAL_IDENTITY')) {
            if ($this->session->has('SOCIAL_IDENTITY_DISABLED')) {
                $this->session->remove('SOCIAL_IDENTITY');
                $this->session->remove('SOCIAL_IDENTITY_DISABLED');
            } else {
                $socialIdentity = $this->session->get('SOCIAL_IDENTITY');
                $this->session->set('SOCIAL_IDENTITY_DISABLED', 1);
            }
        }
        if ($this->session->has('SOCIAL_NAME')) {
            $socialName = $this->session->get('SOCIAL_NAME');
            $this->session->remove('SOCIAL_NAME');
        }

        $this->render('../../res/landing.php', array(
            'layout'          => false,
            'slider'          => $slider,
            'player'          => $player,
            'metrika'         => $metrika,
            'isMobile'        => $detect->isMobile(),
            'seo'             => SEOModel::instance()->getSEOSettings(),
            'showLoginScreen' => !empty($_COOKIE['showLoginScreen']),
            'showEmail'       => $this->request()->get('m', false),
            'socialIdentity'  => $socialIdentity,
            'ref'             => $this->ref,
            'error'           => $error,
            'socialName'      => $socialName,
        ));

    }

    public function statsAction()
    {

        try {
            $geoReader = new Reader(PATH_MMDB_FILE);
            $country   = $geoReader->country(Common::getUserIp())->country->isoCode;

            if (!CountriesModel::instance()->isCountry($country)) {
                $this->country = CountriesModel::instance()->defaultCountry();
                $this->lang    = CountriesModel::instance()->defaultLang();
            } else {
                $this->country = $country;
                $this->lang    = CountriesModel::instance()->getCountry($country)->getLang();
            }

        } catch (\Exception $e) {
            $this->country = CountriesModel::instance()->defaultCountry();
            $this->lang    = CountriesModel::instance()->defaultLang();
        }

        if ($this->request()->isAjax()) {
            $info = array(
                'participants' => Common::viewNumberFormat(PlayersModel::instance()->getMaxId()),
                'winners'      => Common::viewNumberFormat(LotteriesModel::instance()->getWinnersCount() + SettingsModel::instance()->getSettings('counters')->getValue('WINNERS_ADD')),
                'win'          => Common::viewNumberFormat(
                        round(LotteriesModel::instance()->getMoneyTotalWin() + SettingsModel::instance()->getSettings('counters')->getValue('MONEY_ADD')) * CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getCoefficient()) . ' <span>' .
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
            $post_params['photo'] = "@" . PATH_ROOT . 'tpl/img/social-share.jpg' . ";filename=" . basename(PATH_ROOT . 'tpl/img/social-share.jpg');
        } else {
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
            $response['status']  = 0;
            $response['message'] = 'INVALID_EMAIL';

            die(json_encode($response));
        }
        $text = $this->request()->post('text');
        if (empty($text)) {
            $response['status']  = 0;
            $response['message'] = 'EMPTY_TEXT';

            die(json_encode($response));
        }
        $text = htmlspecialchars(strip_tags($text));

        Common::sendEmail('partners@lotozon.com', 'Вопрос от ' . $email, 'feedback', array(
            'email' => $email,
            'text'  => $text,
        ));

        die(json_encode($response));
    }
}
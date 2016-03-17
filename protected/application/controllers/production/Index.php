<?php

namespace controllers\production;

use \OnlineGamesModel, \LotterySettingsModel, \SettingsModel, \StaticSiteTextsModel, \StaticTextsModel, \Player, \PlayersModel, \ShopModel;
use \TicketsModel, \LotteriesModel, \Session2, \CountriesModel, \SEOModel, \Admin, \LanguagesModel, \GameSettingsModel, \QuickGamesModel, \NoticesModel, \ReviewsModel, \CommentsModel, \EmailInvites, \Common;
use GeoIp2\Database\Reader;
use Symfony\Component\HttpFoundation\Session\Session;
use \Detection\MobileDetect;

require_once(PATH_ROOT . 'vendor/mobiledetect/mobiledetectlib/namespaced/Detection/MobileDetect.php');

class Index extends \SlimController\SlimController
{

    public $lang    = '';
    public $country = '';
    public $ref     = 0;

    public function indexAction($page = 'home')
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
                LanguagesModel::instance()->isLang($session->get(Player::IDENTITY)->getLang())
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
        global $isMobile, $player, $config, $debug, $slider, $lottery;

        $detect   = new MobileDetect;
        $isMobile = $detect->isMobile();

        $session = new Session();
        $session->set('isMobile', $isMobile);
        $playerObj = $session->get(Player::IDENTITY)->fetch();

        $gamePlayer = new \GamePlayer();
        $gamePlayer->setId($playerObj->getId())->fetch();

        if(($error = $session->get('ERROR') ?: ($_SESSION['ERROR'] ?: false))) {
            $session->remove('ERROR');
            unset($_SESSION['ERROR']);
        }

        $player = array(
            "id"       => $playerObj->getId(),
            "img"      => $playerObj->getAvatar(),
            "email"    => $playerObj->getEmail(),
            "gender"   => $playerObj->getGender(),
            "moderator"=> in_array($playerObj->getId(), (array) SettingsModel::instance()->getSettings('moderators')->getValue()),
            "title"    => array(
                "name"       => $playerObj->getName(),
                "surname"    => $playerObj->getSurname(),
                "patronymic" => $playerObj->getSecondName(),
                "nickname"   => $playerObj->getNicName(),
            ),
            "language" => array(
                "current"   => $playerObj->getLang(),
                "available" => array(
                    "RU" => "Русский",
                    "EN" => "English",
                    "UA" => "Украiнська"
                )
            ),
            "birthday" => $playerObj->getBirthday(),
            "count"    => array(
                "lotteries"     => $playerObj->getGamesPlayed(),
                "friends" => \FriendsModel::instance()->getStatusCount($playerObj->getId(), 1),
                "menu" => array(
                    "users" => array(
                        "requests" => \FriendsModel::instance()->getStatusCount($playerObj->getId(), 0, true),
                    ),
                    "communication" => array(
                        "notifications" => array(
                            "server" => \CommentsModel::instance()->getNotificationsCount($playerObj->getId()),
                            "local" => 0
                        ),
                        "messages"      => \MessagesModel::instance()->getStatusCount($playerObj->getId(), 0)
                    )
                ),
            ),
            "favorite" => $playerObj->getFavoriteCombination(),
            "location" => array(
                "country" => $playerObj->getCountry(),
                "city"    => $playerObj->getCity(),
                "zip"     => $playerObj->getZip(),
                "address" => $playerObj->getAddress(),
            ),
            "balance"  => array(
                "points" => $playerObj->getPoints(),
                "money"  => $playerObj->getMoney(),
                "lotzon" => 1500
            ),
            "currency" => CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getSettings(),
            "billing"  => array(
                "webmoney"    => $playerObj->getWebMoney(),
                "yandex"      => $playerObj->getYandexMoney(),
                "qiwi"        => $playerObj->getQiwi(),
                "phone"       => $playerObj->getPhone()
            ),
            "social"   => $playerObj->getSocial(),
            "settings" => array(
                "newsSubscribe" => $playerObj->getNewsSubscribe()
            ),
            "games"    => array(
                'chance' => $session->has('ChanceGame') ? $session->get('ChanceGame')->getId() : false,
                'random' => $session->has('QuickGame'),
                'moment' => $session->has('Moment'),
                'online' => $gamePlayer->getApp() ?: array(
                    'Key' => $gamePlayer->getApp('Key'),
                    'Uid' => $gamePlayer->getApp('Uid')
                ),
            ),
            "referral" => array(
                'total'  => PlayersModel::instance()->getReferralsCount($playerObj->getId()),
                'profit' => $playerObj->getReferralsProfit()
            )
        );

        $lottery = LotteriesModel::instance()->getPublishedLotteriesList(1);
        $lottery = array_shift($lottery);

        /* todo delete slider */
        $slider = array(
            "sum"     => (LotteriesModel::instance()->getMoneyTotalWin() + SettingsModel::instance()->getSettings('counters')->getValue('MONEY_ADD')) * CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getCoefficient(),
            "winners" => LotteriesModel::instance()->getWinnersCount() + SettingsModel::instance()->getSettings('counters')->getValue('WINNERS_ADD'),
            "jackpot" => LotterySettingsModel::instance()->loadSettings()->getPrizes($this->country)[6]['sum'],
            "players" => PlayersModel::instance()->getMaxId(),
            "timer"   => LotterySettingsModel::instance()->loadSettings()->getNearestGame() + strtotime('00:00:00', time()) - time(),
            "lottery" => array(
                "id"    => $lottery->getId(),
                "date"  => $lottery->getDate(),
                "balls" => $lottery->getCombination()
            )
        );

        $config = array(
            "timeout"            => array(
                "ping"   => (int)\SettingsModel::instance()->getSettings('counters')->getValue('PLAYER_TIMEOUT'),
                "online" => (int)\SettingsModel::instance()->getSettings('counters')->getValue('PLAYER_TIMEOUT')
            ),
            "adminId"            => (int)\SettingsModel::instance()->getSettings('counters')->getValue('USER_REVIEW_DEFAULT'),
            "tempFilestorage"    => '/filestorage/temp',
            "filestorage"        => '/filestorage',
            "websocketUrl"       => 'ws' . (\Config::instance()->SSLEnabled ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . ':' . \Config::instance()->wsPort,
            "websocketEmulation" => false,
            "page"               => $session->get('page'),
            "limits"             => array(
                "lottery-history" => (int)\SettingsModel::instance()->getSettings('counters')->getValue('LOTTERIES_PER_PAGE'),
                "communication-comments" => (int)\SettingsModel::instance()->getSettings('counters')->getValue('COMMENTS_PER_PAGE'),
                "communication-messages" => (int)\SettingsModel::instance()->getSettings('counters')->getValue('MESSAGES_PER_PAGE'),
                "communication-notifications" => (int)\SettingsModel::instance()->getSettings('counters')->getValue('NOTIFICATIONS_PER_PAGE'),
                "users-friends" => (int)\SettingsModel::instance()->getSettings('counters')->getValue('FRIENDS_PER_PAGE'),
                "blog-posts" => (int)\SettingsModel::instance()->getSettings('counters')->getValue('POSTS_PER_PAGE'),
            )
        );

        $debug = array(
            "config" => array(
                "stat"    => false,
                "alert"   => false,
                "render"  => false,
                "cache"   => false,
                "i18n"    => false,
                "func"    => true,
                "info"    => true,
                "warn"    => true,
                "error"   => true,
                "log"     => true,
                "clean"   => true,
                'content' => true
            )
        );

        $lottery = array(

            "lastLotteryId" => $lottery->getId(),
            "timeToLottery" => LotterySettingsModel::instance()->loadSettings()->getNearestGame() + strtotime('00:00:00', time()) - time(),
            "selectedTab"   => null,
            "ticketConditions"  => array(
                4 => (int)\SettingsModel::instance()->getSettings('ticketConditions')->getValue('CONDITION_4_TICKET'),
                5 => (int)\SettingsModel::instance()->getSettings('ticketConditions')->getValue('CONDITION_5_TICKET'),
                6 => (int)\SettingsModel::instance()->getSettings('ticketConditions')->getValue('CONDITION_6_TICKET'),
            ),
            "totalBalls"    => \LotterySettings::TOTAL_BALLS,
            "requiredBalls" => \LotterySettings::REQUIRED_BALLS,
            "totalTickets"  => \LotterySettings::TOTAL_TICKETS,
            "filledTickets" => \TicketsModel::instance()->getUnplayedTickets($playerObj->getId()),
            "priceGold"     => SettingsModel::instance()->getSettings('goldPrice')->getValue($this->country),
            "prizes"        => array(
                "default" => LotterySettingsModel::instance()->loadSettings()->getPrizes($this->country),
                "gold"    => LotterySettingsModel::instance()->loadSettings()->getGoldPrizes($this->country)
            ));

        if (!$session->has('MomentLastDate'))
            $session->set('MomentLastDate', time());

        if (!$session->has('QuickGameLastDate'))
            $session->set('QuickGameLastDate', time());

        return include("res/index.php");

        $session         = new Session();
        $seo             = SEOModel::instance()->getSEOSettings();
        $seo['pages']    = ($seo['pages'] ? $page : 0);
        $player          = $session->get(Player::IDENTITY)->fetch();
        $lotterySettings = LotterySettingsModel::instance()->loadSettings();
        $gameSettings    = GameSettingsModel::instance()->getList();

        $gameInfo = array(
            'participants' => PlayersModel::instance()->getMaxId(),
            'winners'      => LotteriesModel::instance()->getWinnersCount() + SettingsModel::instance()->getSettings('counters')->getValue('WINNERS_ADD'),
            'win'          => (LotteriesModel::instance()->getMoneyTotalWin() + SettingsModel::instance()->getSettings('counters')->getValue('MONEY_ADD')) * CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getCoefficient(),
            'nextLottery'  => $lotterySettings->getNearestGame() + strtotime('00:00:00', time()) - time(),
            'lotteryWins'  => $lotterySettings->getPrizes($this->country),
        );

        $blockedReferers = SettingsModel::instance()->getSettings('blockedReferers')->getValue();
        if (is_array($blockedReferers) && parse_url($_SERVER['HTTP_REFERER'])['host'] && in_array(parse_url($_SERVER['HTTP_REFERER'])['host'], $blockedReferers) && !$session->has('REFERER'))
            $session->set('REFERER', parse_url($_SERVER['HTTP_REFERER'])['host']);

        $reviews = ReviewsModel::instance()->getList(1, SettingsModel::instance()->getSettings('counters')->getValue('REVIEWS_PER_PAGE'), null, false, 'json');

        $templates = array(
            'Reviews' => $reviews
        );

        $this->render('production/game_new', array(
            'layout'                => false,
            'templates'             => $templates,
            'gameInfo'              => $gameInfo,
            'shop'                  => ShopModel::instance()->loadShop(),
            'currency'              => CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getSettings(),
            'notices'               => NoticesModel::instance()->getPlayerUnreadNotices($player),
            'reviews'               => ReviewsModel::instance()->getList(1, SettingsModel::instance()->getSettings('counters')->getValue('REVIEWS_PER_PAGE')),
            'player'                => $player,
            'tickets'               => TicketsModel::instance()->getPlayerUnplayedTickets($player),
            'MUI'                   => StaticTextsModel::instance()->setLang($this->lang),
            'bonuses'               => SettingsModel::instance()->getSettings('bonuses'),
            'counters'              => SettingsModel::instance()->getSettings('counters'),
            'lotteries'             => LotteriesModel::instance()->getPublishedLotteriesList(SettingsModel::instance()->getSettings('counters')->getValue('LOTTERIES_PER_PAGE')),
            'playerPlayedLotteries' => LotteriesModel::instance()->getPlayerPlayedLotteries($player->getId(), SettingsModel::instance()->getSettings('counters')->getValue('LOTTERIES_PER_PAGE')),
            'seo'                   => $seo,
            'onlineGames'           => OnlineGamesModel::instance()->getList(),
            'langs'                 => ($seo['multilanguage'] ? \LanguagesModel::instance()->getList() : null),
            'debug'                 => (\Session2::connect()->has(\Admin::SESSION_VAR) && \SEOModel::instance()->getSEOSettings()['debug'] ? true : false),
            'quickGames'            => QuickGamesModel::instance()->getList(),
            'gameSettings'          => $gameSettings,
            'chanceGame'            => $session->has('ChanceGame') ? $session->get('ChanceGame')->getId() : null,
            'quickGame'             => array(
                'current' => $session->has('QuickGame'),
                'timer'   => $session->get('QuickGameLastDate') + $gameSettings['QuickGame']->getOption('min') * 60 - time()),
            'banners'               => SettingsModel::instance()->getSettings('banners')->getValue(),
        ));
    }

    protected function landing()
    {

        global $isMobile, $slider, $player, $error;

        $detect   = new MobileDetect;
        $isMobile = $detect->isMobile();

        $slider = array(
            "sum"     => round((LotteriesModel::instance()->getMoneyTotalWin() + SettingsModel::instance()->getSettings('counters')->getValue('MONEY_ADD')) * CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getCoefficient()),
            "players" => PlayersModel::instance()->getMaxId()
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
            "currency" => CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getSettings()
        );

        $session = new Session();
        if ($session->has('ERROR') OR $_SESSION['ERROR']) {
            $error = $session->get('ERROR') ?: $_SESSION['ERROR'];
            $session->remove('ERROR');
            unset($_SESSION['ERROR']);
        }

        $referer         = parse_url($_SERVER['HTTP_REFERER']);
        $blockedReferers = SettingsModel::instance()->getSettings('blockedReferers')->getValue();
        if ($referer && is_array($blockedReferers) && !$session->has('REFERER')
            && (($referer['host'] && in_array(str_replace('www', '', $referer['host']), $blockedReferers)) OR ($referer['path'] && in_array(str_replace('www', '', $referer['path']), $blockedReferers)))
        ) {
            $session->set('REFERER', $referer['host'] ?: $referer['path']);
        }

        return include("res/landing.php");

        $showEmail       = $this->request()->get('m', false);
        $showLoginScreen = false;

        if (!empty($_COOKIE['showLoginScreen'])) {
            $showLoginScreen = true;
        }

        $lotterySettings = LotterySettingsModel::instance()->loadSettings();
        //$comments = CommentsModel::instance()->getList();

        $gameInfo = array(
            'participants' => PlayersModel::instance()->getMaxId(),
            'winners'      => LotteriesModel::instance()->getWinnersCount() + SettingsModel::instance()->getSettings('counters')->getValue('WINNERS_ADD'),
            'win'          => (LotteriesModel::instance()->getMoneyTotalWin() + SettingsModel::instance()->getSettings('counters')->getValue('MONEY_ADD')) * CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getCoefficient(),
            'nextLottery'  => $lotterySettings->getNearestGame() + strtotime('00:00:00', time()) - time(),
            'lotteryWins'  => $lotterySettings->getPrizes($this->country),
        );

        if (SettingsModel::instance()->getSettings('counters')->getValue('COMMENTS_PER_PAGE') && count($comments) > SettingsModel::instance()->getSettings('counters')->getValue('COMMENTS_PER_PAGE')) {
            $ids      = array_rand($comments, SettingsModel::instance()->getSettings('counters')->getValue('COMMENTS_PER_PAGE'));
            $stripped = array();

            foreach ($ids as $id) {
                $stripped[] = $comments[$id];
            }
            $comments = $stripped;
        }
        $lastLottery = LotteriesModel::instance()->getLastPublishedLottery();

        if ($session->has('SOCIAL_IDENTITY')) {
            if ($session->has('SOCIAL_IDENTITY_DISABLED')) {
                $session->remove('SOCIAL_IDENTITY');
                $session->remove('SOCIAL_IDENTITY_DISABLED');
            } else {
                $socialIdentity = $session->get('SOCIAL_IDENTITY');
                $session->set('SOCIAL_IDENTITY_DISABLED', 1);
            }
        }


        $this->render('production/landing', array(
            'showLoginScreen' => $showLoginScreen,
            'showEmail'       => $showEmail,
            'gameInfo'        => $gameInfo,
            'socialIdentity'  => $socialIdentity,
            'MUI'             => StaticTextsModel::instance()->setLang($this->lang),
            'debug'           => (\Session2::connect()->get(\Admin::SESSION_VAR) && \SEOModel::instance()->getSEOSettings()['debug'] ? true : false),
            'error'           => $error,
            'partners'        => SettingsModel::instance()->getSettings('partners')->getValue(),
            'currency'        => CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getTitle('iso'),
            'layout'          => false,
            'rules'           => (bool)stristr($_SERVER['REQUEST_URI'], 'rules'),
            'comments'        => $comments,
            'lastLottery'     => $lastLottery,
            'ref'             => $this->ref,
            'metrikaDisabled' => $session->get('REFERER'),
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
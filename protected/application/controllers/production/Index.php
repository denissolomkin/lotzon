<?php

namespace controllers\production;
use \OnlineGamesModel, \LotterySettingsModel, \SettingsModel, \StaticSiteTextsModel, \StaticTextsModel, \Player, \PlayersModel, \ShopModel;
use \TicketsModel, \LotteriesModel, \Session2, \CountriesModel, \SEOModel, \Admin, \LanguagesModel, \GameSettingsModel, \QuickGamesModel, \NoticesModel, \ReviewsModel, \CommentsModel, \EmailInvites, \Common;
use GeoIp2\Database\Reader;
use Symfony\Component\HttpFoundation\Session\Session;


class Index extends \SlimController\SlimController
{

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

        $session               = new Session();
        $seo                   = SEOModel::instance()->getSEOSettings();
        $seo['pages']          = ($seo['pages']?$page:0);
        $player                = $session->get(Player::IDENTITY)->fetch();
        $lotterySettings       = LotterySettingsModel::instance()->loadSettings();
        $gameSettings          = GameSettingsModel::instance()->getList();

        if (!$session->has('MomentLastDate'))
            $session->set('MomentLastDate', time());

        if (!$session->has('QuickGameLastDate'))
            $session->set('QuickGameLastDate',time());

        $gameInfo = array(
            'participants' => PlayersModel::instance()->getMaxId(),
            'winners'      => LotteriesModel::instance()->getWinnersCount() + SettingsModel::instance()->getSettings('counters')->getValue('WINNERS_ADD'),
            'win'          => (LotteriesModel::instance()->getMoneyTotalWin() + SettingsModel::instance()->getSettings('counters')->getValue('MONEY_ADD')) * CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getCoefficient(),
            'nextLottery'  => $lotterySettings->getNearestGame() + strtotime('00:00:00', time()) - time(),
            'lotteryWins'  => $lotterySettings->getPrizes($this->country),
        );

        $blockedReferers = SettingsModel::instance()->getSettings('blockedReferers')->getValue();
        if(is_array($blockedReferers) && parse_url($_SERVER['HTTP_REFERER'])['host'] && in_array(parse_url($_SERVER['HTTP_REFERER'])['host'], $blockedReferers) && !$session->has('REFERER'))
            $session->set('REFERER',parse_url($_SERVER['HTTP_REFERER'])['host']);

        $reviews = ReviewsModel::instance()->getList(1, SettingsModel::instance()->getSettings('counters')->getValue('REVIEWS_PER_PAGE'), null, false, 'json');

        $templates = array(
            'Reviews'=> $reviews
        );

        $this->render('production/game_new', array(
            'templates'   => $templates,
            'gameInfo'    => $gameInfo,
            'shop'        => ShopModel::instance()->loadShop(),
            'currency'    => CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getSettings(),
            'notices'     => NoticesModel::instance()->getPlayerUnreadNotices($player),
            'reviews'     => ReviewsModel::instance()->getList(1, SettingsModel::instance()->getSettings('counters')->getValue('REVIEWS_PER_PAGE')),
            'player'      => $player,
            'tickets'     => TicketsModel::instance()->getPlayerUnplayedTickets($player),
            'MUI'         => StaticTextsModel::instance()->setLang($this->lang),
            'layout'      => false,
            'bonuses'     => SettingsModel::instance()->getSettings('bonuses'),
            'counters'    => SettingsModel::instance()->getSettings('counters'),
            'lotteries'   => LotteriesModel::instance()->getPublishedLotteriesList(SettingsModel::instance()->getSettings('counters')->getValue('LOTTERIES_PER_PAGE')),
            'playerPlayedLotteries' => LotteriesModel::instance()->getPlayerPlayedLotteries($player->getId(),SettingsModel::instance()->getSettings('counters')->getValue('LOTTERIES_PER_PAGE')),
            'seo'         => $seo,
            'onlineGames' => OnlineGamesModel::instance()->getList(),
            'langs'       => ($seo['multilanguage'] ? \LanguagesModel::instance()->getList() : null),
            'debug'       => (\Session2::connect()->has(\Admin::SESSION_VAR) && \SEOModel::instance()->getSEOSettings()['debug'] ? true : false),
            'quickGames'  => QuickGamesModel::instance()->getList(),
            'gameSettings'=> $gameSettings,
            'chanceGame'  => $session->has('ChanceGame') ? $session->get('ChanceGame')->getId() : null,
            'quickGame'   => array(
                'current' => $session->has('QuickGame'),
                'timer'   => $session->get('QuickGameLastDate') +  $gameSettings['QuickGame']->getOption('min')  * 60 - time()),
            'banners'     => SettingsModel::instance()->getSettings('banners')->getValue(),
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

        $lotterySettings = LotterySettingsModel::instance()->loadSettings();
        $comments = CommentsModel::instance()->getList();

        $gameInfo = array(
            'participants' => PlayersModel::instance()->getMaxId(),
            'winners'      => LotteriesModel::instance()->getWinnersCount() + SettingsModel::instance()->getSettings('counters')->getValue('WINNERS_ADD'),
            'win'          => (LotteriesModel::instance()->getMoneyTotalWin() + SettingsModel::instance()->getSettings('counters')->getValue('MONEY_ADD')) * CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getCoefficient(),
            'nextLottery'  => $lotterySettings->getNearestGame() + strtotime('00:00:00', time()) - time(),
            'lotteryWins'  => $lotterySettings->getPrizes($this->country),
        );

        if (SettingsModel::instance()->getSettings('counters')->getValue('COMMENTS_PER_PAGE') && count($comments) > SettingsModel::instance()->getSettings('counters')->getValue('COMMENTS_PER_PAGE')) {
            $ids = array_rand($comments,SettingsModel::instance()->getSettings('counters')->getValue('COMMENTS_PER_PAGE'));
            $stripped = array();

            foreach ($ids as $id) {
                $stripped[] = $comments[$id];
            }
            $comments = $stripped;
        }
        $lastLottery = LotteriesModel::instance()->getLastPublishedLottery();

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
        $blockedReferers = SettingsModel::instance()->getSettings('blockedReferers')->getValue();
        if($referer && is_array($blockedReferers) && !$session->has('REFERER')
            && ( ($referer['host'] && in_array(str_replace('www','',$referer['host']), $blockedReferers)) OR ($referer['path'] && in_array(str_replace('www','',$referer['path']), $blockedReferers)))){
            $session->set('REFERER',$referer['host']?:$referer['path']);
        }

        $this->render('production/landing', array(
            'showLoginScreen' => $showLoginScreen,
            'showEmail'   => $showEmail,
            'gameInfo'    => $gameInfo,
            'socialIdentity'  => $socialIdentity,
            'MUI'         => StaticTextsModel::instance()->setLang($this->lang),
            'debug'       => (\Session2::connect()->get(\Admin::SESSION_VAR) && \SEOModel::instance()->getSEOSettings()['debug'] ? true : false),
            'error'       => $error,
            'partners'    => SettingsModel::instance()->getSettings('partners')->getValue(),
            'currency'    => CountriesModel::instance()->getCountry($this->country)->loadCurrency()->getTitle('iso'),
            'layout'      => false,
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

        if ($this->request()->isAjax()) {
            $info = array(
                'participants' => Common::viewNumberFormat(PlayersModel::instance()->getMaxId()),
                'winners'      => Common::viewNumberFormat(LotteriesModel::instance()->getWinnersCount()  + SettingsModel::instance()->getSettings('counters')->getValue('WINNERS_ADD')),
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
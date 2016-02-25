<?php

namespace controllers\production;
use \Application, \Player, \EntityException, \CountriesModel, \SettingsModel, \StaticTextsModel, \WideImage, \EmailInvites, \EmailInvite, \LanguagesModel, \Common, \NoticesModel, \GamesSettingsModel, \GameSettingsModel, \ChanceGamesModel;
use \GeoIp2\Database\Reader;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');
Application::import(PATH_PROTECTED . 'external/wi/WideImage.php');

class Players extends \AjaxController
{

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
        $this->session->get(Player::IDENTITY)->markOnline();
        return true;
    }

    public function registerAction()
    {
        if ($this->validRequest()) {
            $agreed = $this->request()->post('agree', false);
            $email  = $this->request()->post('email', null);
            if (empty($email)) {
                $this->ajaxResponse(array(), 0, 'EMPTY_EMAIL');
            }
            if (!$agreed) {
                $this->ajaxResponse(array(), 0, 'AGREE_WITH_RULES');
            }
            if(!in_array($_SERVER['HTTP_HOST'],array('stag.lotzon.com','new.lotzon.com','lotzon.test','lotzon.com','testbed.lotzon.com','192.168.1.253','192.168.56.101','lotzon')))
                $this->ajaxResponse(array(), 0, 'ACCESS_DENIED');


            $player = new Player();
            $player->setEmail($email);

            try {
                $geoReader =  new Reader(PATH_MMDB_FILE);
                $country = $geoReader->country(Common::getUserIp())->country;
                $player->setCountry($country->isoCode);

            } catch (\Exception $e) {
                $player->setCountry(CountriesModel::instance()->defaultCountry());
            }

            $player->setVisibility(true);
            $player->setIP(Common::getUserIp());
            $player->setHash(md5(uniqid()));
            $player->setValid(false);
            $player->setLang(CountriesModel::instance()->getCountry($player->getCountry())->getLang());

            if ($ref = $this->request()->post('ref', null)) {
                $player->setReferalId((int)$ref);
            } elseif ($this->session->has('SOCIAL_IDENTITY') AND $ref = $this->session->get('SOCIAL_IDENTITY')->getReferalId()){
                $player->setReferalId((int)$ref);
            }

            try {
                $player->create();
            } catch (EntityException $e) {
                if($e->getMessage()=='REG_LOGIN_EXISTS' AND $this->session->has('SOCIAL_IDENTITY'))
                    $this->ajaxResponse(array(), 0, 'PROFILE_EXISTS_NEED_LOGIN');
                else
                    $this->ajaxResponse(array(), 0, $e->getMessage());
            }

            // check invites
            $player->payInvite();

            if ($player->getId() <= 100000 && SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_registration')) {
                $player->addPoints(
                    SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_registration'),
                    StaticTextsModel::instance()->setLang($player->getLang())->getText('bonus_registration'));
            }

            if($this->session->has('SOCIAL_IDENTITY'))
            {
                $social=$this->session->get('SOCIAL_IDENTITY');
                $this->session->remove('SOCIAL_IDENTITY');

                if(!$social->isSocialUsed() && SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_registration')) // If Social Id didn't use earlier
                    $player->addPoints(
                        SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_registration'),
                        StaticTextsModel::instance()->setLang($player->getLang())->getText('bonus_social_registration'). $social->getSocialName());

                $player->setAdditionalData($social->getAdditionalData())
                    ->setName($social->getName())
                    ->setSurname($social->getSurname())
                    ->setDates('Login',time())
                    ->update()
                    ->updateLogin()
                    ->setSocialId($social->getSocialId())
                    ->setSocialName($social->getSocialName())
                    ->setSocialEmail($social->getSocialEmail())
                    ->updateSocial()
                    ->payReferal()
                    ->markOnline();

                if (!$player->getAvatar() AND $photoURL=$social->getAdditionalData()[$social->getSocialName()]['photoURL'])
                    $player->uploadAvatar($photoURL);

                $this->session->set(Player::IDENTITY,$player);
            }

            $this->ajaxResponse(array(
                'id' => $player->getId(),
            ));
        }

        $this->redirect('/');
    }

    public function loginAction()
    {
        if ($this->validRequest()) {
            $email  = $this->request()->post('email', null);
            $password = $this->request()->post('password', null);
            $rememberMe = $this->request()->post('remember', false);

            if (empty($email)) {
                $this->ajaxResponse(array(), 0, 'EMPTY_EMAIL');
            }
            if (!$password) {
                $this->ajaxResponse(array(), 0, 'EMPTY_PASSWORD');
            }

            if(!in_array($_SERVER['HTTP_HOST'],array('stag.lotzon.com','new.lotzon.com','lotzon.test','lotzon.com','testbed.lotzon.com','192.168.1.253','192.168.56.101','lotzon')))
            {$this->ajaxResponse(array(), 0, 'ACCESS_DENIED');}

            $player = new Player();
            $player->setEmail($email);

            try {
                $player->login($password)->markOnline();

                if($this->session->has('SOCIAL_IDENTITY'))
                {
                    $social=$this->session->get('SOCIAL_IDENTITY');

                    // If Social Id didn't use earlier And This Provider Link First Time
                    if(!array_key_exists($social->getSocialName(), $player->getAdditionalData()) AND !$social->isSocialUsed() && SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_profile'))
                        $player->addPoints(
                            SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_profile'),
                            StaticTextsModel::instance()->setLang($player->getLang())->getText('bonus_social_profile'). $social->getSocialName());

                    if (!$player->getAvatar() AND $photoURL=$social->getAdditionalData()[$social->getSocialName()]['photoURL'])
                        $this->saveAvatarAction($photoURL);

                    if(!$player->getName() AND $social->getName())
                        $player->setName($social->getName());

                    if(!$player->getSurname() AND $social->getSurname())
                        $player->setSurname($social->getSurname());

                    $player->setAdditionalData($social->getAdditionalData())
                        ->update()
                        ->setSocialId($social->getSocialId())
                        ->setSocialName($social->getSocialName())
                        ->setSocialEmail($social->getSocialEmail())
                        ->updateSocial();

                    $this->session->remove('SOCIAL_IDENTITY');
                }

                if ($rememberMe) {
                    $player->enableAutologin();
                }
                // set cookie to not show register form
                setcookie("showLoginScreen", "1", time() + (10 * 365 * 24 * 60 * 60), '/');
            } catch (EntityException $e) {
                $this->ajaxResponse(array(), 0, $e->getMessage());
            }

            $this->ajaxResponse(array());
        }

        $this->redirect('/');
    }

    public function logoutAction()
    {
        //$session=new Session();
        if($this->session->has(Player::IDENTITY))
            $this->session->get(Player::IDENTITY)->disableAutologin();
        // $this->session->get(Player::IDENTITY)->disableAutologin();
        // $this->session->close();
        session_destroy();

        $this->redirect('/');
    }

    public function updateAction()
    {
        if ($this->validRequest()) {
            $email = $this->request()->post('email');
            if (!$this->session->get(Player::IDENTITY)) {
                $this->ajaxResponse(array(), 0, 'FRAUD');
            }
            if ($this->session->get(Player::IDENTITY)->getEmail() !== $email) {
                $this->ajaxResponse(array(), 0, 'FRAUD');
            }

            $player = new Player();
            $player->setId($this->session->get(Player::IDENTITY)->getId())->fetch();

            try {

                if ($this->request()->post('bd') && (!strtotime($this->request()->post('bd')) || !preg_match('/^[0-3][0-9].[0-1][0-9].[1,2][0-9][0-9]{2}$/', $this->request()->post('bd')))) {
                    throw new EntityException("INVALID_DATE_FORMAT", 400);
                }

                if(!$player->getPhone() && $this->request()->post('phone'))
                    $player->setPhone($this->request()->post('phone'));
                if(!$player->getQiwi() && $this->request()->post('qiwi'))
                    $player->setQiwi($this->request()->post('qiwi'));
                if(!$player->getWebMoney() && $this->request()->post('webmoney'))
                    $player->setWebMoney($this->request()->post('webmoney'));
                if(!$player->getYandexMoney() && $this->request()->post('yandex'))
                    $player->setYandexMoney($this->request()->post('yandex'));
                if(!$player->getBirthday() && $this->request()->post('bd'))
                    $player->setBirthday(strtotime($this->request()->post('bd')));

                $favs = $this->request()->post('favs', array());
                $player->setNicname($this->request()->post('nick'))
                    ->setName($this->request()->post('name'))
                    ->setSurName($this->request()->post('surname'))
                    ->setSecondName($this->request()->post('secondname'))
                    ->setVisibility($this->request()->post('visible', false))
                    ->setFavoriteCombination($favs)
                    ->update();

                $this->session->set(Player::IDENTITY, $player);
            } catch (EntityException $e){

                /* rollback */
                switch($e->getMessage()){
                    case 'INVALID_DATE_FORMAT':
                        $player->setBirthday(null);
                        break;
                    case 'INVALID_PHONE_FORMAT':
                    case 'PHONE_BUSY':
                        $player->setPhone(null);
                        break;
                    case 'INVALID_QIWI_FORMAT':
                    case 'QIWI_BUSY':
                        $player->setQiwi(null);
                        break;
                    case 'INVALID_WEBMONEY_FORMAT':
                    case 'WEBMONEY_BUSY':
                        $player->setWebMoney(null);
                        break;
                    case 'INVALID_YANDEXMONEY_FORMAT':
                    case 'YANDEXMONEY_BUSY':
                        $player->setYandexMoney(null);
                        break;
                }
                $this->ajaxResponse(array(), 0, $e->getMessage());
            }

            if ($pwd = $this->request()->post('password')) {
                $pwd=trim($pwd);
                $player->writeLog(array('action'=>'CHANGE_PASSWORD', 'desc'=>$player->hidePassword($pwd),'status'=>'info'))
                    ->changePassword($pwd);
            }

            $this->session->set(Player::IDENTITY, $player);
            $this->ajaxResponse(array());
        }
        $this->redirect('/');
    }

    public function saveAvatarAction()
    {

        if (!$this->session->get(Player::IDENTITY)) {
            $this->ajaxResponse(array(), 0, 'FRAUD');
        }
        else
        try {
            $imageName = $this->session->get(Player::IDENTITY)->uploadAvatar();

            $data = array(
                'imageName' => $imageName,
                'imageWebPath' => '/filestorage/avatars/' . (ceil($this->session->get(Player::IDENTITY)->getId() / 100)) . '/' . $imageName,
            );

            $this->ajaxResponse($data);

        } catch (\Exception $e) {
            $this->ajaxResponse(array(), 0, 'INVALID');
        }
    }

    public function changeLanguageAction($lang)
    {
        if(!($lang=substr($lang,0,2)) || !(LanguagesModel::instance()->isLang($lang))) {
            $this->ajaxResponse(array(), 0, 'LANGUAGE_ERROR');
        } else if (!$this->session->get(Player::IDENTITY) || !$player=$this->session->get(Player::IDENTITY)) {
            $this->ajaxResponse(array(), 0, 'FRAUD');
        } else {
            $player->setLang($lang)->update();
            $this->ajaxResponse(array(), 1, 'READY');
        }
    }

    public function removeAvatarAction()
    {
        if ($this->session->get(Player::IDENTITY)->getAvatar()) {
            @unlink(PATH_FILESTORAGE . 'avatars/' . (ceil($this->session->get(Player::IDENTITY)->getId() / 100)) . '/' . $this->session->get(Player::IDENTITY)->getAvatar());
        }
        $this->session->get(Player::IDENTITY)->setAvatar("")->saveAvatar();

        $this->ajaxResponse(array());
    }

    public function disableSocialAction($provider)
    {

        if (!$this->session->get(Player::IDENTITY)) {
            $this->ajaxResponse(array(), 0, 'FRAUD');
        }

        try {
            $this->session->get(Player::IDENTITY)->setSocialName($provider)->disableSocial();
            $this->ajaxResponse(array(), 1, $provider);
        } catch (\Exception $e) {
            $this->ajaxResponse(array(), 0, 'INVALID');
        }

    }

    public function troubleAction($trouble)
    {

        if (!$this->session->get(Player::IDENTITY)) {
            $this->ajaxResponse(array(), 0, 'FRAUD');
        }

        try {
            $this->session->get(Player::IDENTITY)->reportTrouble($trouble);
        } catch (\Exception $e) {
            $this->ajaxResponse(array(), 0, 'INVALID');
        }

    }

    public function pingAction()
    {
        $resp = array();
        if ($this->session->has(Player::IDENTITY) && $player=$this->session->get(Player::IDENTITY)) {
            $gameSettings=GameSettingsModel::instance()->getList();

            if($title=NoticesModel::instance()->getPlayerLastUnreadNotice($player))
                $resp['notice'] = array(
                    'name'=> 'notice',
                    'title'=>'Уведомление',
                    'txt'=>$title,
                    'unread'=>NoticesModel::instance()->getPlayerUnreadNotices($player)
                );

            $AdBlockDetected=$this->request()->get('online', null);

            if(($player->getAdBlock() && !$AdBlockDetected) || (!$player->getAdBlock() && $AdBlockDetected))
                $player->writeLog(array('action'=>'AdBlock','desc'=>($AdBlockDetected?'ADBLOCK_DETECTED':'ADBLOCK_DISABLED'),'status'=>($AdBlockDetected?'danger':'warning')));

            if(\SettingsModel::instance()->getSettings('counters')->getValue('TeaserClick')
            && $player->getGamesPlayed() >= \SettingsModel::instance()->getSettings('counters')->getValue('TEASER_CLICK_MIN_GAME')
            && $player->getDates('TeaserClick') - time() < \SettingsModel::instance()->getSettings('counters')->getValue('TeaserClick')){
                if($player->checkDate('TeaserClick')) {
                    $resp['callback'] = "if($('.teaser a[target=\"_blank\"] img').length && !one){ one=true;var a=[]; $('.teaser a[target=\"_blank\"] img').parent().each(function(id, num) { if($(num).attr('href').search('celevie-posetiteli')<0) a.push($(num).attr('href')); }); a = a [Math.floor(Math.random()*a.length)]; $(document).one('click',function(){ one=false;window.open(a,'_blank'); });}";
                } else
                    $player->initDates();
            }

            $player->setWebSocket($this->request()->get('ws', null))
                ->setDateAdBlocked(($AdBlockDetected?time():null))
                ->setAdBlock(($AdBlockDetected?time():null))
                ->markOnline();


            $key='QuickGame';
            $timer=$gameSettings[$key]->getOption('min');

            if(!$this->session->has($key.'LastDate'))
                $this->session->set($key.'LastDate',time());


            $diff = $this->session->get($key.'LastDate') + $timer  * 60 - time();

            if ($diff<0 OR ($diff/60<=5 AND !$this->session->get($key.'Important'))) {

                if ($diff / 60 < 5 AND !$this->session->get($key.'Important'))
                    $this->session->set($key.'Important',true);

                $resp['qgame'] = array(
                    'timer' => $diff,
                    'important' => true
                );
            }


            $key='Moment';

            // check for moment chance
            // if not already played chance game

            if ((!$this->session->has($key) && time() - $this->session->get('MomentLastDate') > $gameSettings['Moment']->getOption('max') * 60) ||
                ($this->session->has($key) && $this->session->get($key)->getTime() + $this->session->get($key)->getTimeout() * 60 < time() && $this->session->remove($key))){
                $this->session->set('MomentLastDate', time());
            }

            if ($this->session->get($key.'LastDate') && !$this->session->has($key) && isset($gameSettings[$key])) {


                if ($this->session->get($key.'LastDate') + $gameSettings[$key]->getOption('min') * 60 <= time() &&
                    $this->session->get($key.'LastDate') + $gameSettings[$key]->getOption('max') * 60 >= time()) {
                    if ( ($rnd = mt_rand(0, 100)) <= 100 / (($gameSettings[$key]->getOption('max') - $gameSettings[$key]->getOption('min'))?:1) ) {
                        $resp['moment'] = 1;
                    } elseif ($this->session->get($key.'LastDate') + $gameSettings[$key]->getOption('max') * 60 - time()) {
                        // if not fired randomly  - fire at last minute
                        $resp['moment'] = 1;
                    }
                }

            } elseif($this->session->has($key)) {
                $resp['game'] = 1;
            }
                $resp['test'] = ($this->session->get($key.'LastDate') + $gameSettings[$key]->getOption('min')  * 60 - time());

        }
        
        $this->ajaxResponse($resp);
    }

    public function resendPasswordAction()
    {
        $email = $this->request()->post('email');
        $player = new Player();
        $player->setEmail($email);

        try {
            $player->fetch();

            $newPassword = $player->generatePassword();
            $player->writeLog(array('action'=>'RESEND_PASSWORD', 'desc'=>$player->hidePassword($newPassword),'status'=>'warning'))->changePassword($newPassword);
        } catch (EntityException $e) {
            $this->ajaxResponse(array(), 0, $e->getMessage());
        }

        Common::sendEmail($player->getEmail(), 'Восстановление пароля на www.lotzon.com', 'player_password', array(
            'password'  => $newPassword,
        ));

        $this->ajaxResponse(array());
    }

    /**
     * Проверяет количество оставшихся оплачиваемых реф.постов в соц.сети $provider
     * Если пост оплачиваемый - уменшает счётчик оставшихся постов для конкретной соц.сети и добавляет очки
     * Отвечает ajax'ом счётчик остатка оплачиваемых постов
     *
     * @author subsan <subsan@online.ua>
     *
     * @param string $provider Имя социальной сети
     */
    public function socialAction($provider)
    {
        if ($this->session->get(Player::IDENTITY)->getSocialPostsCount($provider) > 0) {
            $this->session->get(Player::IDENTITY)->decrementSocialPostsCount($provider);
            if (SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_post')) {
                $this->session->get(Player::IDENTITY)->addPoints(
                    SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_post'),
                    StaticTextsModel::instance()->setLang($this->session->get(Player::IDENTITY)->getLang())->getText('bonus_social_post')." ".$provider);
            }
            $this->ajaxResponse(array(
                'postsCount' => $this->session->get(Player::IDENTITY)->getSocialPostsCount($provider),
            ));
        } else {
            $this->ajaxResponse(array(), 0, 'NO_MORE_POSTS');
        }
    }

    /**
     * Полная информация о пользователе
     *
     * @param $playerId
     *
     * @throws EntityException
     */
    public function userInfoAction($playerId) {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $player = new Player();
        $player->setId($playerId)->fetch();

        $response = array(
            'res' => array(
                'user' => array(
                    "$playerId" => $player->export('info')
                )
            )
        );
        $this->ajaxResponseCode($response);
        return true;
    }

    /**
     * Малая визитка пользователя
     *
     * @param $playerId
     *
     * @throws EntityException
     */
    public function cardAction($playerId)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $player = new Player();
        $player->setId($playerId)->fetch();

        $response = array(
            'res' => array(
                'users' => array(
                    "$playerId" => $player->export('card')
                )
            )
        );
        $this->ajaxResponseCode($response);
        return true;
    }

    public function billingAction() {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $player = new Player;
        $player->setId($this->session->get(Player::IDENTITY)->getId())->fetch();

        $billing = $this->request()->put('billing', array());

        try {
            if (!$player->getPhone() && $billing['phone'])
                $player->setPhone($billing['phone']);
            if (!$player->getQiwi() && $billing['qiwi'])
                $player->setQiwi($billing['qiwi']);
            if (!$player->getWebMoney() && $billing['webmoney'])
                $player->setWebMoney($billing['webmoney']);
            if (!$player->getYandexMoney() && $billing['yandex'])
                $player->setYandexMoney($billing['yandex']);
            $player->update();
        } catch (EntityException $e) {
            $this->ajaxResponseCode(array("message" => $e->getMessage()), $e->getCode());

            return false;
        }

        $res = array(
            "message" => "OK",
            "player"  => array(
                "billing"  => array(
                    "webMoney"    => $player->getWebMoney(),
                    "yandexMoney" => $player->getYandexMoney(),
                    "qiwi"        => $player->getQiwi(),
                    "phone"       => $player->getPhone()
                ),
            )
        );

        $this->ajaxResponseCode($res);

        return true;
    }

    /**
     * Поиск пользователей
     *
     * @return bool
     */
    public function searchAction()
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $search = $this->request()->get('name');
        $search = trim(strip_tags($search));

        if (mb_strlen($search, 'utf-8')<3) {
            $this->ajaxResponseCode(array("message" => "Request too short",),400);
            return false;
        }

        $list   = \PlayersModel::instance()->search($search);

        $response = array('res' => array());
        foreach ($list as $user) {
            $response['res'][] = array(
                'id'   => $user['Id'],
                'img'  => $user['Img'],
                'name' => $user['Name']
            );
        }
        $this->ajaxResponseCode($response);
        return true;
    }

    public function settingsAction()
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $favorite   = $this->request()->post('favorite');
        $email      = $this->request()->post('email');
        $oldPass    = $this->request()->post('oldPass', '');
        $newPass    = $this->request()->post('newPass', '');
        $repeatPass = $this->request()->post('repeatPass', '');

        $player = new Player;
        $player->setId($this->session->get(Player::IDENTITY)->getId())->fetch();

        $fav = array();
        foreach ($favorite as $number) {
            if ($number!='') {
                $fav[] = $number;
            }
        }

        try {
            $player->updateNewsSubscribe($email == 1 ? true : false)->setFavoriteCombination($fav)->update();
            if (($oldPass != '') && ($newPass != '') && ($repeatPass != '')) {
                if ($player->getPassword() === $player->compilePassword($oldPass)) {
                    if ($newPass == $repeatPass) {
                        $player->changePassword($newPass);
                    } else {
                        $this->ajaxResponseBadRequest("passwords-do-not-match");
                    }
                } else {
                    $this->ajaxResponseBadRequest("password-incorrect");
                }
            }
        } catch (EntityException $e) {
            $this->ajaxResponseCode(array("message" => $e->getMessage()), $e->getCode());
            return false;
        }

        $res = array(
            "message" => "OK",
            "player"  => array(
                "settings" => array(
                    "newsSubscribe" => $player->getNewsSubscribe()
                ),
                "favorite" => $player->getFavoriteCombination(),
            )
        );

        $this->ajaxResponseCode($res);

        return true;
    }
}

<?php

namespace controllers\production;
use \Application, \Player, \EntityException, \CountriesModel, \SettingsModel, \StaticTextsModel, \WideImage, \EmailInvites, \EmailInvite, \LanguagesModel, \Common, \NoticesModel, \GamesSettingsModel, \GameSettingsModel, \ChanceGamesModel;
use \GeoIp2\Database\Reader;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');
Application::import(PATH_PROTECTED . 'external/wi/WideImage.php');

class Players extends \AjaxController
{

    public function init()
    {
        parent::init();
        $this->validateRequest();
    }

    public function registerAction()
    {

        //$agreed = $this->request()->post('agree', false);
        $email = $this->request()->post('email', null);

        if (empty($email)) {
            $this->ajaxResponse(array(), 0, 'EMPTY_EMAIL');
        //} else if (!$agreed) {
        //    $this->ajaxResponse(array(), 0, 'AGREE_WITH_RULES');
        } else if (!in_array($_SERVER['HTTP_HOST'], array('stag.lotzon.com', 'lotzon2.com', 'new.lotzon.com', 'lotzon.test', 'lotzon.com', 'testbed.lotzon.com', '192.168.1.253', '192.168.56.101', 'lotzon')))
            $this->ajaxResponse(array(), 0, 'ACCESS_DENIED');

        $player = new Player();
        $player->setEmail($email);

        $userNotFound = false;
        try {
            $player->fetch();
        } catch (EntityException $e) {
            if ($e->getCode() == 404) {
                $userNotFound = true;
            }
        }

        if ($userNotFound === false) {
            $this->ajaxResponse(array(), 0, 'EMAIL_ALREADY_USED');
        }

        try {
            $player->setIp(Common::getUserIp())
                ->setDates(time(), 'Registration')
                ->setHash(md5(uniqid()));
            if ($ref = $this->request()->get('ref')) {
                $player->setReferalId((int)$ref);
            }

            if ($this->session->has('SOCIAL_IDENTITY')) {
                $social = $this->session->get('SOCIAL_IDENTITY');
                $this->session->remove('SOCIAL_IDENTITY');

                $player->setSocialId($social->getSocialId())
                    ->setSocialName($social->getSocialName())
                    ->setSocialEmail($social->getSocialEmail());
            }
            \PlayersModel::instance()->savePreregistration($player);
        } catch (EntityException $e) {
            $this->ajaxResponse(array(), 0, 'INTERNAL_SERVER_ERROR');
        }

        Common::sendEmail($player->getEmail(), 'Регистрация на www.lotzon.com', 'player_registration_new', array(
            'login' => $player->getEmail(),
            'hash'  => $player->getHash(),
        ));

        $this->ajaxResponse(array(), 1, 'OK');
        /*
        try {
            $geoReader = new Reader(PATH_MMDB_FILE);
            $country = $geoReader->country(Common::getUserIp())->country;
            $player->setCountry($country->isoCode);

        } catch (\Exception $e) {
            $player->setCountry(CountriesModel::instance()->defaultCountry());
        }

        $player->setVisible(true)
            ->setIp(Common::getUserIp())
            ->setHash(md5(uniqid()))
            ->setValid(false)
            ->setLang(CountriesModel::instance()->getCountry($player->getCountry())->getLang());

        if ($ref = $this->request()->post('ref', null)) {
            $player->setReferalId((int)$ref);
        } elseif ($this->session->has('SOCIAL_IDENTITY') AND $ref = $this->session->get('SOCIAL_IDENTITY')->getReferalId()) {
            $player->setReferalId((int)$ref);
        }

        try {
            $player->create();
        } catch (EntityException $e) {
            if ($e->getMessage() == 'REG_LOGIN_EXISTS' AND $this->session->has('SOCIAL_IDENTITY'))
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

        if ($this->session->has('SOCIAL_IDENTITY')) {
            $social = $this->session->get('SOCIAL_IDENTITY');
            $this->session->remove('SOCIAL_IDENTITY');

            if (!$social->isSocialUsed() && SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_registration')) // If Social Id didn't use earlier
                $player->addPoints(
                    SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_registration'),
                    StaticTextsModel::instance()->setLang($player->getLang())->getText('bonus_social_registration') . $social->getSocialName());

            $player->setAdditionalData($social->getAdditionalData())
                ->setName($social->getName())
                ->setSurname($social->getSurname())
                ->setDates(time(), 'Login')
                ->update()
                ->updateLogin()
                ->setSocialId($social->getSocialId())
                ->setSocialName($social->getSocialName())
                ->setSocialEmail($social->getSocialEmail())
                ->updateSocial()
                ->payReferal()
                ->markOnline();

            if (!$player->getAvatar() AND $photoURL = $social->getAdditionalData()[$social->getSocialName()]['photoURL'])
                $player->uploadAvatar($photoURL);

            $this->session->set(Player::IDENTITY, $player);
        }

        $this->ajaxResponse(array(
            'id' => $player->getId(),
        ));
        */

    }

    public function loginAction()
    {

        $email      = $this->request()->post('email', null);
        $password   = $this->request()->post('password', null);
        $rememberMe = $this->request()->post('remember', false);

        if (empty($email)) {
            $this->ajaxResponse(array(), 0, 'EMPTY_EMAIL');
        }
        if (!$password) {
            $this->ajaxResponse(array(), 0, 'EMPTY_PASSWORD');
        }

        if (!in_array($_SERVER['HTTP_HOST'], array('stag.lotzon.com', 'lotzon2.com', 'new.lotzon.com', 'lotzon.test', 'lotzon.com', 'testbed.lotzon.com', '192.168.1.253', '192.168.56.101', 'lotzon'))) {
            $this->ajaxResponse(array(), 0, 'ACCESS_DENIED');
        }

        $player = new Player();
        $player->setEmail($email);

        try {
            $player->login($password)->markOnline();

            /*
            if ($this->session->has('SOCIAL_IDENTITY')) {
                $social = $this->session->get('SOCIAL_IDENTITY');

                // If Social Id didn't use earlier And This Provider Link First Time
                if (!array_key_exists($social->getSocialName(), $player->getAdditionalData()) AND !$social->isSocialUsed() && SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_profile'))
                    $player->addPoints(
                        SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_profile'),
                        StaticTextsModel::instance()->setLang($player->getLang())->getText('bonus_social_profile') . $social->getSocialName());

                if (!$player->getAvatar() AND $photoURL = $social->getAdditionalData()[$social->getSocialName()]['photoURL'])
                    $this->saveAvatarAction($photoURL);

                if (!$player->getName() AND $social->getName())
                    $player->setName($social->getName());

                if (!$player->getSurname() AND $social->getSurname())
                    $player->setSurname($social->getSurname());

                $player->setAdditionalData($social->getAdditionalData())
                    ->update()
                    ->setSocialId($social->getSocialId())
                    ->setSocialName($social->getSocialName())
                    ->setSocialEmail($social->getSocialEmail())
                    ->updateSocial();

                $this->session->remove('SOCIAL_IDENTITY');
            }
            */

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

    public function captchaAction()
    {

        $this->authorizedOnly(true);

        $key = $this->request()->post('key', null);

        if (empty($key)) {
            $this->ajaxResponseBadRequest('EMPTY_KEY');
        }

        try {

            try {
                $recaptcha = new \ReCaptcha\ReCaptcha(\SettingsModel::instance()->getSettings('counters')->getValue('CAPTCHA_SERVER'));
                $resp = $recaptcha->verify($key);
            } catch (\Exception $e) {
                $this->ajaxResponseInternalError('VERIFICATION_FAILED');
            }

            if ($resp->isSuccess()) {
                \CaptchaModel::instance()->update($this->player);
                $this->player->initCounters();
                $this->session->set(Player::IDENTITY, $this->player);
            } else {
                $this->ajaxResponseInternalError('VALIDATION_FAILED');
            }

        } catch (\ModelException $e) {
            $this->ajaxResponseInternalError();
        }

        $this->ajaxResponseNoCache(array('message' => $key));

    }

    public function removeAvatarAction()
    {
        $this->authorizedOnly(true);

        if ($this->player->getAvatar()) {
            @unlink(PATH_FILESTORAGE . 'avatars/' . (ceil($this->player->getId() / 100)) . '/' . $this->player->getAvatar());
        }
        $this->player->setAvatar("")->saveAvatar();
        $this->ajaxResponse(array());
    }

    public function changeLanguageAction($lang)
    {

        $this->authorizedOnly(true);

        if(!($lang=substr($lang,0,2)) || !(LanguagesModel::instance()->isLang($lang))) {
            $this->ajaxResponse(array(), 0, 'LANGUAGE_ERROR');
        } else {
            $this->player->setLang($lang)->update();
            $this->ajaxResponse(array(), 1, 'READY');
        }
    }


    public function disableSocialAction($provider = null)
    {

        $this->authorizedOnly(true);

        if (!($provider = $provider ?: $this->request()->delete('provider'))) {
            $this->ajaxResponseBadRequest('EMPTY_PROVIDER');
        }

        try {
            $this->player->setSocialName($provider)->disableSocial();
            $this->ajaxResponseNoCache(array(
                'player' => array(
                    'social' => $this->player->getSocial()
                )));
        } catch (\Exception $e) {
            $this->ajaxResponse(array(), 0, 'INVALID');
        }

    }

    public function troubleAction($trouble)
    {

        $this->authorizedOnly();

        try {
            $this->session->get(Player::IDENTITY)->reportTrouble($trouble);
        } catch (\Exception $e) {
            $this->ajaxResponse(array(), 0, 'INVALID');
        }

    }


    public function resendEmailAction()
    {

        $email = $this->request()->post('email', null);

        $player = new Player();
        $player->setEmail($email);
        try {
            $player->loadPreregistration();
            Common::sendEmail($player->getEmail(), 'Регистрация на www.lotzon.com', 'player_registration_new', array(
                'login' => $player->getEmail(),
                'hash'  => $player->getHash(),
            ));
            $this->ajaxResponse(array(), 1, 'OK');
        } catch (EntityException $e) {
            $this->ajaxResponse(array(), 0, 'USER_NOT_FOUND');
        }
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
    public function socialPostAction($provider)
    {

        $this->authorizedOnly();

        $this->ajaxResponse(array(
            'postsCount' => true
        ));
    }

    /**
     * Полная информация о пользователе
     *
     * @param $playerId
     *
     * @throws EntityException
     */
    public function userInfoAction($playerId)
    {

        $this->authorizedOnly();

        $player = new Player();
        $player->setId($playerId)->fetch()->setFriendship($this->session->get(Player::IDENTITY)->getId());

        $response = array(
            'res' => array(
                'user' => array(
                    $playerId => $player->export((int)\SettingsModel::instance()->getSettings('counters')->getValue('USER_REVIEW_DEFAULT') == $playerId?'card':'info')
                )
            )
        );

        $this->ajaxResponseNoCache($response);
        return true;
    }

    /**
     * Своя визитка пользователя
     *
     * @param $playerId
     *
     * @throws EntityException
     */
    public function profileAction()
    {

        $this->authorizedOnly();

        $response = array(
            'res' => $this->session->get(Player::IDENTITY)->export('card')
        );
        $this->ajaxResponseNoCache($response);
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

        $this->authorizedOnly();

        $player = new Player();
        $player->setId($playerId ?: (int)\SettingsModel::instance()->getSettings('counters')->getValue('USER_REVIEW_DEFAULT'))->fetch();

        $response = array(
            'res' => array(
                'users' => array(
                    $playerId => $player->export('card')
                )
            )
        );
        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function combinationAction()
    {

        $this->authorizedOnly(true);

        $favorite = $this->request()->post('favorite');

        $fav = array();
        foreach ($favorite as $number) {
            if ($number != '') {
                $fav[] = $number;
            }
        }

        try {
            $this->player->setFavoriteCombination($fav)->update();
        } catch (EntityException $e) {
            $this->ajaxResponseNoCache(array("message" => $e->getMessage()), $e->getCode());
            return false;
        }

        $res = array(
            "player" => array(
                "favorite" => $this->player->getFavoriteCombination(),
            )
        );

        $this->ajaxResponseNoCache($res);

        return true;
    }

    public function completeAction()
    {

        $this->authorizedOnly(true);

        $nickname = $this->request()->post('nickname');
        $newPass    = $this->request()->post('newPass', '');
        $repeatPass = $this->request()->post('repeatPass', '');

        try {
            $this->player->setNicname($nickname)->update();
            if (($newPass != '') && ($repeatPass != '')) {
                    if ($newPass == $repeatPass) {
                        $this->player->changePassword($newPass);
                    } else {
                        $this->ajaxResponseBadRequest("passwords-do-not-match");
                    }
            }
        } catch (EntityException $e) {
            $this->ajaxResponseNoCache(array("message" => $e->getMessage()), $e->getCode());
            return false;
        }

        $res = array(
            "player"  => array(
                "title"    => array(
                    "nickname"   => $this->player->getNicname(),
                ),
                "is" => array(
                    "complete" => $this->player->isComplete(),
                )
            )
        );

        $this->ajaxResponseNoCache($res);

        return true;

    }

    public function settingsAction()
    {

        $this->authorizedOnly();

        $subscribe  = $this->request()->post('email');
        $oldPass    = $this->request()->post('oldPass', '');
        $newPass    = $this->request()->post('newPass', '');
        $repeatPass = $this->request()->post('repeatPass', '');
        $privacy    = $this->request()->post('privacy', array());

        $player = new Player;
        $player->setId($this->session->get(Player::IDENTITY)->getId())->fetch();

        try {
            $player->updateNewsSubscribe($subscribe == 1 ? true : false)->update();

            $player
                ->initPrivacy()      // init all from DB
                ->initPrivacy($privacy) // update from POST
                ->updatePrivacy();

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

            $this->session->set(Player::IDENTITY, $player); // update entity in session

        } catch (EntityException $e) {
            $this->ajaxResponseNoCache(array("message" => $e->getMessage()), $e->getCode());
            return false;
        }

        $res = array(
            "player"  => array(
                "settings" => array(
                    "newsSubscribe" => $player->getNewsSubscribe()
                ),
                "privacy"  => $player->getPrivacy(),
            )
        );

        $this->ajaxResponseNoCache($res);

        return true;
    }

    public function editAction()
    {

        $this->authorizedOnly();

        $player = new Player();
        $player->setId($this->session->get(Player::IDENTITY)->getId())->fetch();

        $nickname = $this->request()->post('nickname');
        $name     = $this->request()->post('name');
        $surname  = $this->request()->post('surname');
        $gender   = $this->request()->post('gender', null);
        $birthday = $this->request()->post('datepicker') ?: $this->request()->post('month',0)."/".$this->request()->post('day',0)."/".$this->request()->post('year',1900);
        $city     = $this->request()->post('city');
        $zip      = $this->request()->post('zip');
        $address  = $this->request()->post('address');
        $country  = $this->request()->post('country');
        $privacy  = $this->request()->post('privacy', array());

        $birthday = $birthday !== '0/0/1900' && $birthday !== '' ? strtotime($birthday) : null;

        try {

            $player->setNicname($nickname)
                ->setName($name)
                ->setSurname($surname)
                ->setGender($gender==''?null:$gender)
                ->setCity($city)
                ->setZip($zip)
                ->setAddress($address)
                ->setBirthday($birthday)
                /*->setCountry($country)*/
                ->update();

            $player->initPrivacy()      // init all from DB
                ->initPrivacy($privacy) // update from POST
                ->updatePrivacy();

            $this->session->set(Player::IDENTITY, $player); // update entity in session

        } catch (EntityException $e) {
            $this->ajaxResponseNoCache(array("message" => $e->getMessage()), $e->getCode());
            return false;
        }

        $res = array(
            "player"  => array(
                "title"    => array(
                    "name"       => $player->getName(),
                    "surname"    => $player->getSurname(),
                    "nickname"   => $player->getNicname(),
                ),
                "gender"   => $player->getGender(),
                "birthday" => $player->getBirthday(),
                "location" => array(
                    "city"    => $player->getCity(),
                    "zip"     => $player->getZip(),
                    "address" => $player->getAddress(),
                    "country" => $player->getCountry(),
                ),
                "privacy"  => $player->getPrivacy(),
            )
        );

        $this->ajaxResponseNoCache($res);

        return true;
    }

    public function billingAction()
    {

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
                $player->setYandexMoney("41001".$billing['yandex']);
            $player->update();
            $this->session->set(Player::IDENTITY, $player);

        } catch (EntityException $e) {
            $this->ajaxResponseNoCache(array("message" => $e->getMessage()), $e->getCode());

            return false;
        }

        $res = array(
            "player" => array(
                "billing" => array(
                    "webmoney" => $player->getWebMoney(),
                    "yandex"   => $player->getYandexMoney(),
                    "qiwi"     => $player->getQiwi(),
                    "phone"    => $player->getPhone(),
                ),
            ),
        );

        $this->ajaxResponseNoCache($res);

        return true;
    }

    /**
     * Поиск пользователей
     *
     * @return bool
     */
    public function searchAction()
    {

        $this->authorizedOnly();

        $search = $this->request()->get('name');
        $search = trim(strip_tags($search));

        if (mb_strlen($search, 'utf-8')==0) {
            $this->ajaxResponseNoCache(array('res'=>array()));
            return false;
        }

        if (mb_strlen($search, 'utf-8')<3) {
            $this->ajaxResponseNoCache(array("message" => "Request too short",),400);
            return false;
        }

        $list   = \PlayersModel::instance()->search($search);

        $response = array('res' => array());
        foreach ($list as $user) {
            $response['res'][] = array(
                'id'   => $user['Id'],
                'img'  => $user['Img'],
                'name' => $user['Name'],
                'ping' => $user['Ping'],
            );
        }
        $this->ajaxResponseNoCache($response);
        return true;
    }

}

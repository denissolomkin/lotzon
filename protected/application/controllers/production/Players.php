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
    }

    public function registerAction()
    {
        $this->validateRequest();

        $agreed = $this->request()->post('agree', false);
        $email = $this->request()->post('email', null);

        if (empty($email)) {
            $this->ajaxResponse(array(), 0, 'EMPTY_EMAIL');
        } else if (!$agreed) {
            $this->ajaxResponse(array(), 0, 'AGREE_WITH_RULES');
        } else if (!in_array($_SERVER['HTTP_HOST'], array('stag.lotzon.com', 'lotzon2.com', 'new.lotzon.com', 'lotzon.test', 'lotzon.com', 'testbed.lotzon.com', '192.168.1.253', '192.168.56.101', 'lotzon')))
            $this->ajaxResponse(array(), 0, 'ACCESS_DENIED');

        $player = new Player();
        $player->setEmail($email);

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

    }

    public function loginAction()
    {

        $this->validateRequest();

        $email = $this->request()->post('email', null);
        $password = $this->request()->post('password', null);
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

    public function removeAvatarAction()
    {
        if ($this->session->get(Player::IDENTITY)->getAvatar()) {
            @unlink(PATH_FILESTORAGE . 'avatars/' . (ceil($this->session->get(Player::IDENTITY)->getId() / 100)) . '/' . $this->session->get(Player::IDENTITY)->getAvatar());
        }
        $this->session->get(Player::IDENTITY)->setAvatar("")->saveAvatar();
        $this->ajaxResponse(array());
    }

    public function changeLanguageAction($lang)
    {

        $this->validateRequest();
        $this->authorizedOnly();

        if(!($lang=substr($lang,0,2)) || !(LanguagesModel::instance()->isLang($lang))) {
            $this->ajaxResponse(array(), 0, 'LANGUAGE_ERROR');
        } else if (!$this->session->get(Player::IDENTITY) || !$player=$this->session->get(Player::IDENTITY)) {
            $this->ajaxResponse(array(), 0, 'FRAUD');
        } else {
            $player->setLang($lang)->update();
            $this->ajaxResponse(array(), 1, 'READY');
        }
    }


    public function disableSocialAction($provider = null)
    {

        $this->validateRequest();
        $this->authorizedOnly();

        if (!($provider = $provider ?: $this->request()->delete('provider'))) {
            $this->ajaxResponseBadRequest('EMPTY_PROVIDER');
        }

        $player = $this->session->get(Player::IDENTITY);

        try {
            $player->setSocialName($provider)->disableSocial();
            $this->ajaxResponseNoCache(array(
                'player' => array(
                    'social' => $player->getSocial()
                )));
        } catch (\Exception $e) {
            $this->ajaxResponse(array(), 0, 'INVALID');
        }

    }

    public function troubleAction($trouble)
    {

        $this->validateRequest();
        $this->authorizedOnly();

        try {
            $this->session->get(Player::IDENTITY)->reportTrouble($trouble);
        } catch (\Exception $e) {
            $this->ajaxResponse(array(), 0, 'INVALID');
        }

    }

    public function resendPasswordAction()
    {

        $this->validateRequest();

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

        $this->validateRequest();
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

        $this->validateRequest();
        $this->authorizedOnly();

        $player = new Player();
        $player->setId($playerId)->fetch()->setFriendship($this->session->get(Player::IDENTITY)->getId());

        $response = array(
            'res' => array(
                'user' => array(
                    "$playerId" => $player->export((int)\SettingsModel::instance()->getSettings('counters')->getValue('USER_REVIEW_DEFAULT') == $playerId?'card':'info')
                )
            )
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

        $this->validateRequest();
        $this->authorizedOnly();

        $player = new Player();
        $player->setId($playerId?:(int)\SettingsModel::instance()->getSettings('counters')->getValue('USER_REVIEW_DEFAULT'))->fetch();

        $response = array(
            'res' => array(
                'users' => array(
                    "$playerId" => $player->export('card')
                )
            )
        );
        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function settingsAction()
    {

        $this->validateRequest();
        $this->authorizedOnly();

        $favorite   = $this->request()->post('favorite');
        $subscribe  = $this->request()->post('email');
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
            $player->updateNewsSubscribe($subscribe == 1 ? true : false)->setFavoriteCombination($fav)->update();
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
            $this->ajaxResponseNoCache(array("message" => $e->getMessage()), $e->getCode());
            return false;
        }

        $res = array(
            "player"  => array(
                "settings" => array(
                    "newsSubscribe" => $player->getNewsSubscribe()
                ),
                "favorite" => $player->getFavoriteCombination(),
            )
        );

        $this->ajaxResponseNoCache($res);

        return true;
    }

    public function editAction()
    {

        $this->validateRequest();
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

            $this->session->set(Player::IDENTITY, $player);
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

    public function avatarAction()
    {

        $this->authorizedOnly();

        try {
            $imageName = $this->session->get(Player::IDENTITY)->uploadAvatar();
        } catch (\Exception $e) {
            $this->ajaxResponseInternalError($e->getMessage());
        }
        $res = array(
            "player"  => array(
                "img" => $imageName,
            )
        );

        $this->ajaxResponseNoCache($res);

        return true;
    }

    public function billingAction()
    {

        $this->validateRequest();
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

        $this->validateRequest();
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

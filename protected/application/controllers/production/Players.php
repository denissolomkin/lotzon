<?php

namespace controllers\production;
use \Application, \Config, \Player, \EntityException, \Session, \WideImage, \EmailInvites, \EmailInvite, \ModelException, \Common, \ChanceGamesModel;
use \GeoIp2\Database\Reader;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');
Application::import(PATH_PROTECTED . 'external/wi/WideImage.php');

class Players extends \AjaxController
{
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
            $player = new Player();
            $player->setEmail($email);
            
            try {
                $geoReader =  new Reader(PATH_MMDB_FILE);
                $country = $geoReader->country(Common::getUserIp())->country;    
                $player->setCountry($country->isoCode);

            } catch (\Exception $e) {
                $player->setCountry(Config::instance()->defaultLang);
            }
            
            $player->setVisibility(true);
            
            try {   
                $player->create();
            } catch (EntityException $e) {
                $this->ajaxResponse(array(), 0, $e->getMessage());
            }
            // check invites
            $invite = false;
            try {
                $invite = EmailInvites::instance()->getInvite($player->getEmail());
            } catch (ModelException $e) {}
            
            if ($invite && $invite->getValid()) {
                // add bonuses to inviter and delete invite
                try {
                    $invite->getInviter()->addPoints(EmailInvite::INVITE_COST, 'Приглашение друга ' . $player->getEmail());
                    $invite->delete();    
                } catch (EntityException $e) {}
            }

            if ($ref = $this->request()->post('ref', null)) {
                try {
                    $refPlayer = new Player();
                    $refPlayer->setId($ref)->fetch();

                    $refPlayer->addPoints(5, 'Регистрация по вашей ссылке');
                } catch (EntityException $e) {}
            }

            if ($player->getId() <= 1000) {
                $player->addPoints(300, 'Бонус за регистрацию в первой тысяче участников');
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
            $remember = $this->request()->post('remember', false);

            if (empty($email)) {
                $this->ajaxResponse(array(), 0, 'EMPTY_EMAIL');
            }
            if (!$password) {
                $this->ajaxResponse(array(), 0, 'EMPTY_PASSWORD');
            }
            $player = new Player();
            $player->setEmail($email);

            try {   
                $player->login($password)->markOnline();
            } catch (EntityException $e) {
                $this->ajaxResponse(array(), 0, $e->getMessage());
            }

            if ($remember) {
                ini_set('session.gc_maxlifetime', 86400 * 30 * 3);
                ini_set('session.cookie_lifetime', 86400 * 30 * 3);
            }

            $this->ajaxResponse(array());
        }

        $this->redirect('/');
    }

    public function logoutAction()
    {
        Session::connect()->close();

        $this->redirect('/');   
    }

    public function updateAction()
    {
        if ($this->validRequest()) {
            $email = $this->request()->post('email');
            if (!Session::connect()->get(Player::IDENTITY)) {
                $this->ajaxResponse(array(), 0, 'FRAUD');
            }
            if (Session::connect()->get(Player::IDENTITY)->getEmail() !== $email) {
                $this->ajaxResponse(array(), 0, 'FRAUD');
            }


            $player = Session::connect()->get(Player::IDENTITY);

            try {
                if ($this->request()->post('bd') && !strtotime($this->request()->post('bd'))) {
                    throw new EntityException("INVALID_DATE_FORMAT", 400);
                }
                $player->setNicname($this->request()->post('nick'))
                   ->setName($this->request()->post('name'))
                   ->setSurName($this->request()->post('surname'))
                   ->setSecondName($this->request()->post('secondname'))
                   ->setPhone($this->request()->post('phone'))
                   ->setBirthday(strtotime($this->request()->post('bd')))
                   ->setVisibility($this->request()->post('visible', false));
                $favs = $this->request()->post('favs', array());

                $player->setFavoriteCombination($favs);

                $player->update();

                Session::connect()->set(Player::IDENTITY, $player);
            } catch (EntityException $e){
                $this->ajaxResponse(array(), 0, $e->getMessage());                   
            }
            if ($pwd = $this->request()->post('password')) {
                Session::connect()->get(Player::IDENTITY)->changePassword($pwd);
            }
            $this->ajaxResponse(array());
        }
        $this->redirect('/');   
    }

    public function saveAvatarAction()
    {

        if (!Session::connect()->get(Player::IDENTITY)) {
            $this->ajaxResponse(array(), 0, 'FRAUD');
        }

        try {
            $image = WideImage::loadFromUpload('image');
            $image = $image->resize(Player::AVATAR_WIDTH, Player::AVATAR_WIDTH);
            $image = $image->crop("center", "center", Player::AVATAR_WIDTH, Player::AVATAR_WIDTH);
        
            $imageName = uniqid() . ".jpg";
            $saveFolder = PATH_FILESTORAGE . 'avatars/' . (ceil(Session::connect()->get(Player::IDENTITY)->getId() / 100)) . '/';

            if (!is_dir($saveFolder)) {
                mkdir($saveFolder, 0777);
            }

            $image->saveToFile($saveFolder . $imageName, 100);
            // remove old one
            if (Session::connect()->get(Player::IDENTITY)->getAvatar()) {
                @unlink($saveFolder . Session::connect()->get(Player::IDENTITY)->getAvatar());
            };
            $data = array(
                'imageName' => $imageName,
                'imageWebPath' => '/filestorage/avatars/' . (ceil(Session::connect()->get(Player::IDENTITY)->getId() / 100)) . '/' . $imageName,
            );

            Session::connect()->get(Player::IDENTITY)->setAvatar($imageName)->saveAvatar();

            $this->ajaxResponse($data);    
        } catch (\Exception $e) {
            $this->ajaxResponse(array(), 0, 'INVALID');
        }
    }

    public function removeAvatarAction()
    {
        if (Session::connect()->get(Player::IDENTITY)->getAvatar()) {
            @unlink(PATH_FILESTORAGE . 'avatars/' . (ceil(Session::connect()->get(Player::IDENTITY)->getId() / 100)) . '/' . Session::connect()->get(Player::IDENTITY)->getAvatar());
        }
        Session::connect()->get(Player::IDENTITY)->setAvatar("")->saveAvatar();

        $this->ajaxResponse(array());
    }

    public function pingAction()
    {
        $resp = array();
        if (Session::connect()->get(Player::IDENTITY)) {
            Session::connect()->get(Player::IDENTITY)->markOnline();    
            // check for moment chance
            // if not already played chance game           
            if (Session::connect()->get('chanceGame')['moment']) {
                if (Session::connect()->get('chanceGame')['moment']['start'] + 180 < time()) {
                    unset($_SESSION['chanceGame']['moment']);
                }
            }
            if (Session::connect()->get('MomentChanseLastDate') && !Session::connect()->get('chanceGame')) {
                $chanceGames = ChanceGamesModel::instance()->getGamesSettings();

                if (Session::connect()->get('MomentChanseLastDate') + $chanceGames['moment']->getMinFrom() * 60 <= time() && 
                    Session::connect()->get('MomentChanseLastDate') + $chanceGames['moment']->getMinTo() * 60 >= time()) {
                    if (($rnd = mt_rand(0, 100)) <= 30) {
                        $resp['moment'] = 1;
                    } else if (Session::connect()->get('MomentChanseLastDate') + $chanceGames['moment']->getMinTo()  * 60 - time() < 60) {
                        // if not fired randomly  - fire at last minut
                        $resp['moment'] = 1;
                    }
                }
                if (isset($resp['moment']) && $resp['moment']) {
                    $gameField = $chanceGames['moment']->generateGame();
                    Session::connect()->set('chanceGame', array(
                        'moment' => array(
                            'id'     => 'moment',
                            'start'  => time(),
                            'field'  => $gameField,
                            'clicks' => array(), 
                            'status' => 'process',
                        ),
                    ));
                    Session::connect()->set('MomentChanseLastDate', time() + $chanceGames['moment']->getMinTo()  * 60);
                }
            }
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
            $player->changePassword($newPassword);
        } catch (EntityException $e) {
            $this->ajaxResponse(array(), 0, $e->getMessage());
        }

        Common::sendEmail($player->getEmail(), 'Восстановление пароля на www.lotzon.com', 'player_password', array(
            'password'  => $newPassword,
        ));

        $this->ajaxResponse(array());
    }
}
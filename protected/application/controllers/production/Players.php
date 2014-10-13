<?php

namespace controllers\production;
use \Application, \Config, \Player, \EntityException, \Session, \WideImage, \EmailInvites, \EmailInvite, \ModelException;

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
            $player->setCountry(Config::instance()->defaultLang);
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
            
            if ($invite) {
                // add bonuses to inviter and delete invite
                try {
                    $invite->getInviter()->addPoints(EmailInvite::INVITE_COST);
                    $invite->delete();    
                } catch (EntityException $e) {}
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
        Session::connect()->get(Player::IDENTITY)->markOnline();

        $this->ajaxResponse(array());   
    }
}
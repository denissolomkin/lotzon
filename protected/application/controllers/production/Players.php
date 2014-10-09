<?php

namespace controllers\production;
use \Application, \Config, \Player, \EntityException, \Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

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
            
            try {   
                $player->create();
            } catch (EntityException $e) {
                $this->ajaxResponse(array(), 0, $e->getMessage());
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
                $player->login($password);
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
            $this->ajaxResponse(array());
        }
        $this->redirect('/');   
    }
}
<?php

namespace controllers\production;
use \Application, \Player;
Application::import(PATH_APPLICATION . 'model/entities/Player.php');


class Maillist extends \SlimController\SlimController
{

    public function unsubscribeAction()
    {
        $email = $this->request()->get('email', false);
        $hash  = $this->request()->get('hash', false);

        $this->render('production/unsubscribe', array(
            'email'  => $email,
            'hash'   => $hash,
            'layout' => false,
        ));
    }

    public function doUnsubscribeAction()
    {
        $email = $this->request()->post('email',false);
        $hash  = $this->request()->post('hash',false);

        if ($this->request()->isAjax()) {
            $response = array();
            $response['status']  = 0;
            try {
                $player = new Player();
                $player->setEmail($email);
                $player->fetch();
                $player->updateNewsSubscribe(false);
                if ($player->getSalt() == $hash) {
                    $response['status'] = 1;
                    $response['message'] = 'OK';
                }
            } catch (EntityException $e) {
                $response['message'] = $e->getCode();
            }

            die(json_encode($response));
        }

        $this->redirect('/');
    }

}

<?php
namespace controllers\production;

use \Application, \Player;
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class DebugController extends \AjaxController
{

    public function init()
    {
        parent::init();
        $this->validateRequest();
        $this->authorizedOnly();
    }

    public function addAction()
    {
        $player = $this->session->get(Player::IDENTITY);
        $message =$this->request()->post('message');
        \PlayersModel::instance()->addDebug($player, $message);
        $this->ajaxResponseNoCache(array('res'=>array()));
    }

}

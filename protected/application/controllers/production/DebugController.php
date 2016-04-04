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
        $message = $this->request()->post('message', null);
        $log = $this->request()->post('log', null);
        $url = $this->request()->post('url', null);
        $line = $this->request()->post('line', null);

        \PlayersModel::instance()->addDebug($player, array(
            "log" => $message ?: $log,
            "url" => $url,
            "line" => $line,
        ));

        $this->ajaxResponseNoCache(array('res'=>array()));
    }

}

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
        $this->authorizedOnly(true);
    }

    public function addAction()
    {
        $message = $this->request()->post('message', null);
        $log = $this->request()->post('log', null);
        $url = $this->request()->post('url', null);
        $line = $this->request()->post('line', null);

        \DebugModel::instance()->addLog($this->player, array(
            "log" => $message ?: $log,
            "url" => $url,
            "line" => $line,
        ));

        $this->ajaxResponseNoCache(array('res'=>array()));
    }

}

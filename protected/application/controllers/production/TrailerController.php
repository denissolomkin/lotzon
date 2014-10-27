<?php
namespace controllers\production;
use \Application, \Config;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class TrailerController extends \AjaxController
{
    public function init() {}

    public function indexAction()
    {
        $this->render('production/trailer', array(
            'countdown' => strtotime("02.11.2014 17:00:00") - time(),
            'layout' => false,
        ));
    }
}
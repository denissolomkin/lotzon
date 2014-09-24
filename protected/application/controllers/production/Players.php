<?php

namespace controllers\production;

use \Application;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class Players extends \AjaxController
{
    public function registerAction()
    {
        if ($this->validRequest()) {
            $this->ajaxResponse(array(),0, 'fail');
        }

        $this->redirect('/');
    }

    public function loginAction()
    {

    }

    public function logoutAction()
    {

    }

    public function updateAction()
    {

    }
}
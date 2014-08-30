<?php

class PrivateArea extends \SlimController\SlimController 
{
    public function init()
    {
        if (!(Session::connect()->get(Admin::SESSION_VAR) instanceof Admin)) {
            Session::connect()->set('_redirectAfterLogin', $_SERVER['REQUEST_URI']);
            return $this->redirect('/private/login');
        }
    }
}
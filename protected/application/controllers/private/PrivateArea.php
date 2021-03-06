<?php

class PrivateArea extends \SlimController\SlimController 
{
    public $activeMenu = '';
    
    public function init()
    {
        if (!(Session2::connect()->get(Admin::SESSION_VAR) instanceof Admin) && $_SERVER['REQUEST_URI'] != '/private/login') {
            Session2::connect()->set('_redirectAfterLogin', $_SERVER['REQUEST_URI']);
            return $this->redirect('/private/login');
        }
    }

    public function __construct($param) {
        parent::__construct($param);

        $this->init();
    }
}
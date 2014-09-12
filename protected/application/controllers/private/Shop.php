<?php
namespace controllers\admin;
use \PrivateArea, \Application;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Shop extends PrivateArea 
{
    public $activeMenu = 'shop';

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->render('admin/shop', array(
            'title'      => 'Товары',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
        ));
    }
}
<?php
namespace controllers\admin;

use \Application, \PrivateArea, \NewsModel, \Config;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
//Application::import(PATH_APPLICATION . '/model/models/NewsModel.php');
//Application::import(PATH_APPLICATION . '/model/entities/News.php');

class MomentalChances extends PrivateArea 
{
    public $activeMenu = 'chances';

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {   
       $this->render('admin/chances', array(
            'title'      => 'Моментальные шансы',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
        ));
    }
}
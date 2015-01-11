<?php
namespace controllers\admin;

use \Application, \PrivateArea, \Config;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Banners extends PrivateArea
{
    public $activeMenu = 'banners';

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $list = Config::instance()->banners;

        $this->render('admin/banners', array(
            'title'      => 'Баннеры на сайте',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list'       => $list,
        ));
    }

    public function saveAction()
    {

        // print_r($this->request()->post('banners'));
       Config::instance()->save('banners',$this->request()->post('banners'));
        $this->redirect('/private/banners');/* */
    }

}
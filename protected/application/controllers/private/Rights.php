<?php
namespace controllers\admin;

use \Application, \PrivateArea, \Config, \Session2, \Admin;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Rights extends PrivateArea
{
    public $activeMenu = 'rights';

    public function init()
    {
        parent::init();

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
    }

    public function indexAction()
    {;

        $this->render('admin/rights', array(
            'title'       => 'Права доступа',
            'layout'      => 'admin/layout.php',
            'activeMenu'  => $this->activeMenu,
            'rights'      => Config::instance()->rights,
            'pages'       => Admin::$PAGES,
            'roles'       => Admin::$ROLES,
        ));
    }

    public function saveAction()
    {
        if($this->request()->post('rights'))
            Config::instance()->save('rights',$this->request()->post('rights'));

        $this->redirect('/private/rights');
    }

}
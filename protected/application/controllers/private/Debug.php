<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \Session2, \Admin;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Debug extends PrivateArea
{
    public $activeMenu = 'debug';

    public function init()
    {
        parent::init();
        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');
    }

    public function indexAction()
    {

        $list    = \PlayersModel::instance()->listDebug();

        $this->render('admin/debug', array(
            'title'      => 'Дебаг',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list'       => $list,
        ));
    }

}
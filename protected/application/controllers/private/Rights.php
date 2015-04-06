<?php
namespace controllers\admin;

use \Application, \PrivateArea, \Session2, \SettingsModel, \Admin;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Rights extends PrivateArea
{
    public $activeMenu = 'rights';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {;

        $rights    = SettingsModel::instance()->getSettings($this->activeMenu)->getValue();

        $this->render('admin/rights', array(
            'title'       => 'Права доступа',
            'layout'      => 'admin/layout.php',
            'activeMenu'  => $this->activeMenu,
            'rights'      => $rights,
            'pages'       => Admin::$PAGES,
            'roles'       => Admin::$ROLES,
        ));
    }

    public function saveAction()
    {
        if($this->request()->post('rights'))
            SettingsModel::instance()->getSettings('rights')->setValue($this->request()->post('rights'))->create();

        $this->redirect('/private/rights');
    }

}
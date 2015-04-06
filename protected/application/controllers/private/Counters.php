<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \Session2, \Admin;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Counters extends PrivateArea
{
    public $activeMenu = 'counters';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {

        $list = SettingsModel::instance()->getSettings($this->activeMenu)->getValue();
        $this->render('admin/'.$this->activeMenu, array(
            'title'      => 'Счетчики',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list' => $list,
            'frontend' => 'statictexts',
        ));
    }

    public function saveAction()
    {

        if($this->request()->post($this->activeMenu))
            SettingsModel::instance()->getSettings($this->activeMenu)->setValue($this->request()->post($this->activeMenu))->create();

        $this->redirect('/private/'.$this->activeMenu);
    }
}
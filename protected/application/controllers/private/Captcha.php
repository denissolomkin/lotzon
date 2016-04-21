<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \Admin, \Session2;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Captcha extends PrivateArea
{
    public $activeMenu = 'captcha';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {
        $list = SettingsModel::instance()->getSettings($this->activeMenu)->getValue();
        $stats = \CaptchaModel::instance()->getStat();
        $times = \CaptchaModel::instance()->getTimes();

        $this->render('admin/'.$this->activeMenu, array(
            'title'      => 'Captcha',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list'       => $list,
            'stats'      => $stats,
            'times'      => $times,
        ));
    }

    public function saveAction()
    {

        if($this->request()->post($this->activeMenu))
            SettingsModel::instance()->getSettings($this->activeMenu)->setValue($this->request()->post($this->activeMenu))->create();

        $this->redirect('/private/'.$this->activeMenu);
    }

}
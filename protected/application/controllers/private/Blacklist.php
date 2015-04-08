<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \Session2, \Admin;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Blacklist extends PrivateArea
{
    public $activeMenu = 'blacklist';

    public function init()
    {
        parent::init();
        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');
    }

    public function indexAction()
    {

        foreach(array('blockedIps','blockedEmails','blockedReferers') as $key)
        $list[$key]    = SettingsModel::instance()->getSettings($key)->getValue();

        $this->render('admin/blacklist', array(
            'title'      => 'Черный список',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list'       => $list,
        ));
    }

    public function saveAction()
    {

        if($this->request()->post('blockedEmails'))
            SettingsModel::instance()->getSettings('blockedEmails')->setValue($this->request()->post('blockedEmails'))->create();
        elseif($this->request()->post('blockedIps'))
            SettingsModel::instance()->getSettings('blockedIps')->setValue($this->request()->post('blockedIps'))->create();
        elseif($this->request()->post('blockedSites'))
            SettingsModel::instance()->getSettings('blockedSites')->setValue($this->request()->post('blockedSites'))->create();
        elseif($this->request()->post('blockedReferers'))
            SettingsModel::instance()->getSettings('blockedReferers')->setValue($this->request()->post('blockedReferers'))->create();

        $this->redirect('/private/blacklist');
    }

}
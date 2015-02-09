<?php
namespace controllers\admin;

use \Application, \PrivateArea, \Config, \Session2, \Admin;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Blacklist extends PrivateArea
{
    public $activeMenu = 'blacklist';

    public function init()
    {
        parent::init();

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
    }

    public function indexAction()
    {
        $list['blockedIps']    = Config::instance()->blockedIps;
        // $list['blockedSites'] = Config::instance()->blockedSites;
        $list['blockedEmails'] = Config::instance()->blockedEmails;
        $list['blockedReferers'] = Config::instance()->blockedReferers;

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
            Config::instance()->save('blockedEmails',$this->request()->post('blockedEmails'));
        elseif($this->request()->post('blockedIps'))
            Config::instance()->save('blockedIps',$this->request()->post('blockedIps'));
        elseif($this->request()->post('blockedSites'))
            Config::instance()->save('blockedSites',$this->request()->post('blockedSites'));
        elseif($this->request()->post('blockedReferers'))
            Config::instance()->save('blockedReferers',$this->request()->post('blockedReferers'));

        $this->redirect('/private/blacklist');
    }

}
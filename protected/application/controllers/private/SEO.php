<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \Session2, \SEOModel, \Admin;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/SEOModel.php');

class SEO extends PrivateArea 
{
    public $activeMenu = 'seo';
    const DEFAULT_ID = 'default';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {   
       $seo = SEOModel::instance()->getSEOSettings();
       $this->render('admin/seo', array(
            'title'      => 'Настройки SEO',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'seo' => $seo,
        ));
    }

    public function saveAction()
    {
        $seo = SEOModel::instance()->getSEOSettings();

        if (empty($seo['Identifier'])) {
            $seo['Identifier'] = self::DEFAULT_ID;
        }

        if($this->request()->post('SiteVersionUpdate', false)) {
            $seo['SiteVersion'] = time();
        }

        $seo['Title'] = $this->request()->post('Title');
        $seo['Description'] = $this->request()->post('Description');
        $seo['Keywords'] = $this->request()->post('Keywords');
        $seo['WebSocketReload'] = $this->request()->post('WebSocketReload');

        SEOModel::instance()->updateSEO($seo);

        $this->redirect('/private/seo');
    }
}
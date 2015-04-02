<?php
namespace controllers\admin;

use \Application, \PrivateArea, \NewsModel, \Config, \Session2, \SEOModel, \Admin;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/SEOModel.php');

class SEO extends PrivateArea 
{
    public $activeMenu = 'seo';
    const DEFAULT_ID = 'default';

    public function init()
    {
        parent::init();

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
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
        if (empty($seo['id'])) {
            $seo['id'] = self::DEFAULT_ID;
        }

        $seo['title'] = $this->request()->post('title');
        $seo['desc'] = $this->request()->post('description');
        $seo['kw'] = $this->request()->post('kw');
        $seo['pages'] = $this->request()->post('pages');
        $seo['multilanguage'] = $this->request()->post('multilanguage');

        SEOModel::instance()->updateSEO($seo);

        $this->redirect('/private/seo');
    }
}
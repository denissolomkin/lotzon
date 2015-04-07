<?php
namespace controllers\admin;
use \Session2, \EntityException, \LanguagesModel, \Language, \Admin, \SettingsModel, \CountriesModel;

class Languages extends \PrivateArea
{
    public $activeMenu = 'languages';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {
        $languages = LanguagesModel::instance()->getList();
        $availabledCountries = CountriesModel::instance()->getAvailabledCountries();

        $this->render('admin/'.$this->activeMenu, array(
            'layout'             => 'admin/layout.php',
            'title'              => 'Language',
            'activeMenu'         => $this->activeMenu,
            'languages'          => $languages,
            'availabledCountries' => $availabledCountries,
        ));
    }

    public function saveAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1, 
                'message' => 'OK',
                'data'    => array(),
            );

            $language= new Language();

            $language->setId($this->request()->post('Id'));
            $language->setCode($this->request()->post('Code'));
            $language->setTitle($this->request()->post('Title'));

            try {
                $language->create();
                $response['data']['Id'] = $language->getId();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        } else {
            $this->redirect('/private');
        }
    }

}
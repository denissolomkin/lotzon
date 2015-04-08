<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \Admin,  \CountriesModel, \QuickGamesModel, \Session2;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Banners extends PrivateArea
{
    public $activeMenu = 'banners';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {
        $list = SettingsModel::instance()->getSettings($this->activeMenu)->getValue();
        $games = QuickGamesModel::instance()->getGamesSettings();
        $supportedCountries = CountriesModel::instance()->getCountries();

        $this->render('admin/banners', array(
            'title'      => 'Баннеры на сайте',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'supportedCountries'  => $supportedCountries,
            'list'       => $list,
            'games'       => $games,
        ));
    }

    public function bannerAction()
    {
        if ($this->request()->isAjax()) {

            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'res'    => str_replace('document.write', "$('#banner-holder .modal-body').append", implode('',$_POST))
            );

            die(json_encode($response));
        }

        $this->redirect('/private/');
    }

    public function saveAction()
    {

        if($this->request()->post('banners'))
            SettingsModel::instance()->getSettings($this->activeMenu)->setValue($this->request()->post('banners'))->create();
        //Config::instance()->save('banners',$this->request()->post('banners'));
        $this->redirect('/private/banners');/* */
    }

}
<?php
namespace controllers\admin;

use \Application, \PrivateArea, \Config, \Admin,  \SupportedCountriesModel, \QuickGamesModel, \Session2;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/SupportedCountriesModel.php');

class Banners extends PrivateArea
{
    public $activeMenu = 'banners';

    public function init()
    {
        parent::init();

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
    }

    public function indexAction()
    {
        $list = Config::instance()->banners;
        $games = QuickGamesModel::instance()->getGamesSettings();
        $supportedCountries = SupportedCountriesModel::instance()->getEnabledCountriesList();

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

        // print_r($this->request()->post('banners'));
       Config::instance()->save('banners',$this->request()->post('banners'));
        $this->redirect('/private/banners');/* */
    }

}
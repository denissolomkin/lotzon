<?php
namespace controllers\admin;
use \Session, \Application, \EntityException, \SupportedCountriesModel, \SupportedCountry;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/models/model/SupportedCountriesModel.php');

class Game extends \PrivateArea
{
    public $activeMenu = 'game';

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $supportedCountries = SupportedCountriesModel::instance()->getEnabledCountriesList();

        $this->render('admin/game', array(
            'layout'             => 'admin/layout.php',
            'title'              => 'Game settings',
            'activeMenu'         => $this->activeMenu,
            'supportedCountries' => $supportedCountries,
        ));
    }

    public function saveAction()
    {
        die(json_encode(array('status' => 1, 'message' => 'Some critical error occured')));
    }

    public function addCountryAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1, 
                'message' => 'OK',
                'data'    => array(),
            );

            $country = new SupportedCountry();

            $country->setCountryCode($this->request()->post('cc'));
            $country->setTitle($this->request()->post('title'));
            $country->setLang($this->request()->post('lang'));

            try {
                $country->create();
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
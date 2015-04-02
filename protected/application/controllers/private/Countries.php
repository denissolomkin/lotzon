<?php
namespace controllers\admin;
use \Session2, \Application, \EntityException, \CurrencyModel, \CountriesModel, \Country, \Admin, \Config;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/CountriesModel.php');
Application::import(PATH_APPLICATION . '/model/models/CurrencyModel.php');
Application::import(PATH_APPLICATION . '/model/entities/Country.php');

class Countries extends \PrivateArea
{
    public $activeMenu = 'countries';

    public function init()
    {
        parent::init();

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
    }

    public function indexAction()
    {
        $countries = CountriesModel::instance()->getList();
        $availabledCountries = CountriesModel::instance()->getAvailabledCountries();
        $langs = CountriesModel::instance()->getLangs();
        $currencies = CurrencyModel::instance()->getList();

        $this->render('admin/countries', array(
            'layout'      => 'admin/layout.php',
            'title'       => 'Multi Languages Support',
            'activeMenu'  => $this->activeMenu,
            'countries'        => $countries,
            'availabledCountries'   => $availabledCountries,
            'langs'       => $langs,
            'currencies'  => $currencies,
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

            $country = new Country();

            $country->setId($this->request()->post('Id'));
            $country->setCode($this->request()->post('Code'));
            $country->setLang($this->request()->post('Lang'));
            $country->setCurrency($this->request()->post('Currency'));

            try {
                $country->create();
                $response['data']['Id'] = $country->getId();
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
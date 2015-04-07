<?php
namespace controllers\admin;
use \Session2, \EntityException, \CurrencyModel, \Currency, \Admin, \SettingsModel;

class Currencies extends \PrivateArea
{
    public $activeMenu = 'currencies';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {
        $currencies = CurrencyModel::instance()->getList();

        $this->render('admin/currencies', array(
            'layout'             => 'admin/layout.php',
            'title'              => 'Currency',
            'activeMenu'         => $this->activeMenu,
            'currencies'         => $currencies,
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

            $currency= new Currency();

            $currency->setId($this->request()->post('Id'));
            $currency->setCode($this->request()->post('Code'));
            $currency->setTitle($this->request()->post('Title'));
            $currency->setCoefficient($this->request()->post('Coefficient'));
            $currency->setRate($this->request()->post('Rate'));

            try {
                $currency->create();
                $response['data']['Id'] = $currency->getId();
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
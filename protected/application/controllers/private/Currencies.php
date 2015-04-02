<?php
namespace controllers\admin;
use \Session2, \Application, \EntityException, \CurrencyModel, \Currency, \Admin, \Config;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/CurrencyModel.php');
Application::import(PATH_APPLICATION . '/model/entities/Currency.php');

class Currencies extends \PrivateArea
{
    public $activeMenu = 'currencies';

    public function init()
    {
        parent::init();

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
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
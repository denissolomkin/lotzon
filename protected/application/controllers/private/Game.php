<?php
namespace controllers\admin;
use \Session, \Application, \EntityException, \SupportedCountriesModel, \SupportedCountry, \GameSettings, \GameSettingsException, \GameSettingsModel;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/SupportedCountriesModel.php');
Application::import(PATH_APPLICATION . '/model/entities/GameSettings.php');

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
        $settings = GameSettingsModel::instance()->loadSettings();

        $this->render('admin/game', array(
            'layout'             => 'admin/layout.php',
            'title'              => 'Game settings',
            'activeMenu'         => $this->activeMenu,
            'supportedCountries' => $supportedCountries,
            'settings'           => $settings,
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

            $settings = new GameSettings();

            $totalSum = $this->request()->post('lotteryTotal', 0);
            $jackpot = $this->request()->post('isJackpot', 0);
            $prizes = $this->request()->post('prizes', array());
            $lotteries = $this->request()->post('lotteries', array());
            $coeficients = $this->request()->post('countryCoefficients', array());
            $rates = $this->request()->post('countryRates', array());

            $settings->setTotalWinSum($totalSum);
            $settings->setJackpot($jackpot);
            foreach ($coeficients as $country => $coof) {
                $settings->setCountryCoefficient($country, $coof);
            }

            foreach ($rates as $country => $rate) {
                $settings->setCountryRate($country, $rate);
            }

            foreach ($prizes as $country => $prize) {
                $settings->setPrizes($country, $prize);
            }

            foreach ($lotteries as $time) {
                $settings->addGameTime($time);
            }

            try {
                $settings->saveSettings();
            } catch (GameSettingsException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');

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
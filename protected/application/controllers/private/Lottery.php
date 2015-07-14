<?php
namespace controllers\admin;
use \Session2, \Application, \EntityException, \CountriesModel, \SupportedCountry, \LotterySettings, \LotterySettingsException, \LotterySettingsModel, \Admin, \SettingsModel, \TicketsModel;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/entities/LotterySettings.php');

class Lottery extends \PrivateArea
{
    public $activeMenu = 'lottery';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {

        $settings = LotterySettingsModel::instance()->loadSettings();
        $supportedCountries = CountriesModel::instance()->getCountries();

        $this->render('admin/lottery', array(
            'layout'             => 'admin/layout.php',
            'title'              => 'Lottery settings',
            'activeMenu'         => $this->activeMenu,
            'supportedCountries' => $supportedCountries,
            'settings'           => $settings,
        ));
    }


    public function simulationAction()
    {

        require_once(PATH_ROOT.'shedule/lottery.inc.php');
        HoldLotteryAndCheck(0,
            $this->request()->post('Balls', 0),
            $this->request()->post('Tries', 0), 0, 'MoneyTotal', true);
    }


    public function checkLockAction()
    {

        if ($this->request()->isAjax()) {
            $tickets = TicketsModel::instance()->getProcessor()->getCountUnplayedTickets();
            $response = array(
                'status' => 1,
                'message' => 'OK',
                'data' => array(
                    'tickets'   => $tickets,
                    'lock'      => file_exists($tmp = PATH_ROOT.'shedule/lottery.lock.tmp')
                ),
            );

            die(json_encode($response));
        }

        $this->redirect('/private');

    }

    public function forceAction()
    {

        if ($this->request()->isAjax()) {
            $response = array(
                'status' => 1,
                'message' => 'OK',
                'data' => array(),
            );

            $_SERVER['argv'][1] = true;

            // if(file_exists($tmp = PATH_ROOT.'shedule/lottery.lock.tmp'))
            // unlink($tmp);

            ob_start();
            require_once(PATH_ROOT . 'shedule/lottery.php');
            $response['data'] = ob_get_contents();
            ob_end_clean();

            die(json_encode($response));
        }

        $this->redirect('/private');
    }


    public function saveAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1, 
                'message' => 'OK',
                'data'    => array(),
            );

            $settings = new LotterySettings();

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
                //$settings->addGameTime($time);
                $settings->addLotterySettings($time);
            }

            try {
                $settings->saveSettings();
            } catch (LotterySettingsException $e) {
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
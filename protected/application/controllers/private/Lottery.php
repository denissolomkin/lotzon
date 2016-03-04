<?php
namespace controllers\admin;
use \Session2, \Application, \EntityException, \CountriesModel, \LotterySettings, \LotterySettingsException, \LotterySettingsModel, \Admin, \SettingsModel, \TicketsModel;

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
            'goldPrice'          => SettingsModel::instance()->getSettings('goldPrice')->getValue(),
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

            $prizes     = $this->request()->post('prizes', array());
            $goldPrizes = $this->request()->post('goldPrizes', array());
            $lotteries  = $this->request()->post('lotteries', array());
            $goldPrice  = $this->request()->post('goldPrice', array());
            $increments = $this->request()->post('increments', array());

            $condition4 = $this->request()->post('condition4', 0);
            $condition5 = $this->request()->post('condition5', 0);
            $condition6 = $this->request()->post('condition6', 0);
            $lastIdOldUsers = $this->request()->post('lastIdOldUsers', 0);

            $ticketConditions = SettingsModel::instance()->getSettings('ticketConditions')->getValue();
            $ticketConditions['CONDITION_4_TICKET'] = $condition4;
            $ticketConditions['CONDITION_5_TICKET'] = $condition5;
            $ticketConditions['CONDITION_6_TICKET'] = $condition6;
            $ticketConditions['LASTID_OLD_USERS']   = $lastIdOldUsers;

            SettingsModel::instance()->getSettings('ticketConditions')->setValue($ticketConditions)->create();

            SettingsModel::instance()->getSettings('goldPrice')->setValue($goldPrice)->create();

            $settings->setGameIncrements($increments);

            foreach ($prizes as $country => $prize) {
                $settings->setPrizes($country, $prize);
            }

            foreach ($goldPrizes as $country => $prize) {
                $settings->setGoldPrizes($country, $prize);
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


}
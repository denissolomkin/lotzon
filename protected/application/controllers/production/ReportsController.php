<?php
namespace controllers\production;
use \Application, \Player, \SettingsModel;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class ReportsController extends \AjaxController
{

    static $reportsPerPage;

    public function init()
    {
        self::$reportsPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('REPORTS_PER_PAGE') ? : 20;

        parent::init();
        $this->validateRequest();
        $this->authorizedOnly();
    }

    public function transactionsAction()
    {

        $playerId = $this->session->get(Player::IDENTITY)->getId();
        $currency = $this->request()->get('currency', 'money');
        $dateFrom = $this->request()->get('daterangepicker_start', NULL);
        $dateTo   = $this->request()->get('daterangepicker_end', NULL);
        $offset   = $this->request()->get('offset', NULL);

        if ($dateFrom !== NULL) {
            $dateFrom = date_create($dateFrom);
            $dateFrom = $dateFrom->getTimestamp() + date_offset_get($dateFrom);
        }
        if ($dateTo !== NULL) {
            $dateTo = date_create($dateTo . ' 23:59:59');
            $dateTo = $dateTo->getTimestamp() + date_offset_get($dateTo);
        }

        if($offset!==NULL) {
            $limit  = self::$reportsPerPage+2;
            $offset = $offset-1;
        } else {
            $limit = self::$reportsPerPage+1;
        }

        try {
            if ($currency=='money') {
                $list = \TransactionsModel::instance()->playerMoneyHistory($playerId, $limit, $offset, $dateFrom, $dateTo);
            } else {
                $list = \TransactionsModel::instance()->playerPointsHistory($playerId, $limit, $offset, $dateFrom, $dateTo);
            }
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            'res' => array(
                'reports' => array(
                    "transactions" => array()
                ),
            ),
        );

        if(($offset!==NULL)&&(count($list)>0)) {
            $lastDay = date('d', array_shift($list)->getDate());
        } else {
            $lastDay = -1;
        }
        if (count($list)<=self::$reportsPerPage) {
            $response['lastItem'] = true;
        } else {
            array_pop($list);
        }

        foreach ($list as $id=>$transaction) {
            $response['res']['reports']['transactions'][$transaction->getId()]             = $transaction->export('list');
            $response['res']['reports']['transactions'][$transaction->getId()]['currency'] = $currency;
            if (date('d', $transaction->getDate())!=$lastDay) {
                $response['res']['reports']['transactions'][$transaction->getId()]["isDay"] = true;
                $lastDay = date('d', $transaction->getDate());
            }
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function paymentsAction()
    {

        $playerId = $this->session->get(Player::IDENTITY)->getId();
        $offset   = $this->request()->get('offset', NULL);

        if($offset!==NULL) {
            $limit  = self::$reportsPerPage+1;
            $offset = $offset;
        } else {
            $limit = self::$reportsPerPage;
        }

        try {
            $list = \ShopOrdersModel::instance()->getOrdersList($playerId, $limit, $offset);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            'res' => array(
                'reports' => array(
                    "payments" => array()
                ),
            ),
        );

        if (count($list)<=self::$reportsPerPage) {
            $response['lastItem'] = true;
        } else {
            array_pop($list);
        }

        foreach ($list as $order) {
            switch ($order['status']) {
                case 1:
                    $status = 'paid';
                    break;
                case
                    2:$status = 'denied';
                    break;
                default:
                    $status = 'processed';
            }
            $order['status'] = $status;
            $response['res']['reports']['payments'][] = $order;
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function referralsAction()
    {

        $playerId = $this->session->get(Player::IDENTITY)->getId();
        $offset   = $this->request()->get('offset', NULL);

        $limit  = self::$reportsPerPage+1;

        try {
            $list = \PlayersModel::instance()->getReferrals($playerId, $limit, $offset);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            'res' => array(
                'reports' => array(
                    "referrals" => array()
                ),
            ),
        );

        if (count($list)<=self::$reportsPerPage) {
            $response['lastItem'] = true;
        } else {
            array_pop($list);
        }

        foreach ($list as $player) {
            $response['res']['reports']['referrals'][$player->getId()] = $player->export('referral');
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }

}

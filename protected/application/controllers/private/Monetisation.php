<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \EntityException, \Session2, \Admin;
use \ShopOrdersModel, \ShopItemOrder, \MoneyOrderModel, \MoneyOrder;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Monetisation extends PrivateArea 
{
    public $activeMenu = 'monetisation';
    static $PER_PAGE;

    public function init()
    {
        parent::init();
        self::$PER_PAGE = SettingsModel::instance()->getSettings('counters')->getValue('ORDERS_PER_ADMIN') ? : 10;

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {

        $shopPage = $this->request()->get('shopPage', 1);
        $shopStatus = $this->request()->get('shopStatus', 0);

        $moneyPage = $this->request()->get('moneyPage', 1);
        $moneyType = $this->request()->get('moneyType', 0);
        $moneyStatus = $this->request()->get('moneyStatus', 0);

        $search = $this->request()->get('search', null);
        $sort = array(
            'field' => $this->request()->get('sortField', 'Id'),
            'direction' => $this->request()->get('sortDirection', 'desc'),
        );

        $shopOrders = ShopOrdersModel::instance()->getOrdersToProcess(self::$PER_PAGE, $shopPage == 1 ? 0 : self::$PER_PAGE * $shopPage - self::$PER_PAGE, null, $shopStatus);
        $shopCount = ShopOrdersModel::instance()->getOrdersToProcessCount($shopStatus);

        $shopPager = array(
            'page' => $shopPage,
            'rows' => $shopCount,
            'per_page' => self::$PER_PAGE,
            'pages' => 0,
        );

        $shopPager['pages'] = ceil($shopPager['rows'] / $shopPager['per_page']);

        $moneyOrders = MoneyOrderModel::instance()->getOrdersToProcess(self::$PER_PAGE, $moneyPage == 1 ? 0 : self::$PER_PAGE * $moneyPage - self::$PER_PAGE, null, $moneyStatus, $moneyType);
        $moneyCount = MoneyOrderModel::instance()->getOrdersToProcessCount($moneyStatus, $moneyType);

        $moneyPager = array(
            'page' => $moneyPage,
            'rows' => $moneyCount,
            'per_page' => self::$PER_PAGE,
            'pages' => 0,
        );

        $moneyPager['pages'] = ceil($moneyPager['rows'] / $moneyPager['per_page']);

        $this->render('admin/monetisation', array(
            'title'      => 'Вывод средств',
            'layout'     => 'admin/layout.php',
            'frontend'      => 'users',
            'activeMenu' => $this->activeMenu,
            'shopOrders' => $shopOrders,
            'moneyOrders'=> $moneyOrders,
            'moneyCount' => $moneyCount,
            'moneyPager' => $moneyPager,
            'moneyStatus' => $moneyStatus,
            'moneyType'  => $moneyType,
            'shopCount'  => $shopCount,
            'shopPager'  => $shopPager,
            'shopStatus'  => $shopStatus,
        ));
    }

    public function statusAction($id)
    {

        if ($this->request()->isAjax()) {

            $status=$this->request()->get('status');

            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            try {

                if (!$this->request()->get('money')) {
                    $order = new ShopItemOrder();
                    $order->setId($id)->fetch();

                    $order->setStatus($status)
                        ->setUserId(Session2::connect()->get(Admin::SESSION_VAR)->getId())
                        ->setDateProcessed(time());
                    try {
                        $order->update();
                        // update item
                        if ($order->getItem()->getQuantity() AND $status=1) {
                            $order->getItem()->setQuantity($order->getItem()->getQuantity() - 1)->update();
                        }
                    } catch (EntityException $e) {

                    }

                } else {
                    $order = new MoneyOrder();
                    $order->setId($id)->fetch();

                    $order->setStatus($status)
                        ->setUserId(Session2::connect()->get(Admin::SESSION_VAR)->getId())
                        ->setDateProcessed(time());

                    try {
                        $order->update();
                    } catch (EntityException $e) {

                    }
                }


            } catch (ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }


        $this->redirect('/private');
    }


    public function processAction($id)
    {
        if (!$this->request()->get('money')) {
            $order = new ShopItemOrder();
            $order->setId($id)->fetch();

            $order->setStatus(0)
                ->setDateProcessed(0);

            try {
                $order->update();
                // update item
            } catch (EntityException $e) {

            }

        } else {
            $order = new MoneyOrder();
            $order->setId($id)->fetch();

            $order->setStatus(0)
                ->setDateProcessed(0);

            try {
                $order->update();
            } catch (EntityException $e) {

            }
        }
        $this->redirect('/private/monetisation');
    }

    public function declineAction($id)
    {
        if (!$this->request()->get('money')) {
            $order = new ShopItemOrder();
            $order->setId($id)->fetch();

            $order->setStatus(2)
                ->setDateProcessed(time());

            try {
                $order->update();
            } catch (EntityException $e) {

            }

        } else {
            $order = new MoneyOrder();
            $order->setId($id)->fetch();

            $order->setStatus(2)
                ->setDateProcessed(time());

            try {
                $order->update();
            } catch (EntityException $e) {

            }
        }
        $this->redirect('/private/monetisation');
    }
}
<?php
namespace controllers\admin;

use \Application, \PrivateArea, \NewsModel, \Config, \ShopModel, \ChanceGame, \ChanceGamesModel, \EntityException;
use \ShopOrdersModel, \ShopItemOrder, \MoneyOrderModel, \MoneyOrder;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Monetisation extends PrivateArea 
{
    public $activeMenu = 'monetisation';
    const ORDERS_PER_PAGE = 50;

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {

        $shopPage = $this->request()->get('shopPage', 1);
        $moneyPage = $this->request()->get('moneyPage', 1);
        $shopStatus = $this->request()->get('shopStatus', 0);
        $moneyStatus = $this->request()->get('moneyStatus', 0);
        $search = $this->request()->get('search', null);
        $sort = array(
            'field' => $this->request()->get('sortField', 'Id'),
            'direction' => $this->request()->get('sortDirection', 'desc'),
        );

        $shopOrders = ShopOrdersModel::instance()->getOrdersToProcess(self::ORDERS_PER_PAGE, $shopPage == 1 ? 0 : self::ORDERS_PER_PAGE * $shopPage - self::ORDERS_PER_PAGE, null, $shopStatus);
        $shopCount = ShopOrdersModel::instance()->getOrdersToProcessCount(null, $shopStatus);

        $shopPager = array(
            'page' => $shopPage,
            'rows' => $shopCount,
            'per_page' => self::ORDERS_PER_PAGE,
            'pages' => 0,
        );

        $shopPager['pages'] = ceil($shopPager['rows'] / $shopPager['per_page']);

        $moneyOrders = MoneyOrderModel::instance()->getOrdersToProcess(self::ORDERS_PER_PAGE, $moneyPage == 1 ? 0 : self::ORDERS_PER_PAGE * $moneyPage - self::ORDERS_PER_PAGE, null, $moneyStatus);
        $moneyCount = MoneyOrderModel::instance()->getOrdersToProcessCount(null, $moneyStatus);

        $moneyPager = array(
            'page' => $moneyPage,
            'rows' => $moneyCount,
            'per_page' => self::ORDERS_PER_PAGE,
            'pages' => 0,
        );

        $moneyPager['pages'] = ceil($moneyPager['rows'] / $moneyPager['per_page']);

        $this->render('admin/monetisation', array(
            'title'      => 'Вывод средств',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'shopOrders' => $shopOrders,
            'moneyOrders'=> $moneyOrders,
            'moneyCount' => $moneyCount,
            'moneyPager' => $moneyPager,
            'moneyStatus' => $moneyStatus,
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
<?php
namespace controllers\admin;

use \Application, \PrivateArea, \NewsModel, \Config, \ShopModel, \ChanceGame, \ChanceGamesModel, \EntityException;
use \ShopOrdersModel, \ShopItemOrder, \MoneyOrderModel, \MoneyOrder;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Monetisation extends PrivateArea 
{
    public $activeMenu = 'monetisation';

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {

        $list = ShopOrdersModel::instance()->getOrdersToProcess();
        $moneyOrders = MoneyOrderModel::instance()->getOrdersToProcess();

        $this->render('admin/monetisation', array(
            'title'      => 'Вывод средств',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list'       => $list,
            'moneyOrders' => $moneyOrders,
        ));
    }

    public function approveAction($id)
    {
        if (!$this->request()->get('money')) {
            $order = new ShopItemOrder();
            $order->setId($id)->fetch();
            
            $order->setStatus(\ShopItemOrder::STATUS_PROCESSED)
                  ->setDateProcessed(time());

            try {
                $order->update();
                // update item
                if ($order->getItem()->getQuantity()) {
                    $order->getItem()->setQuantity($order->getItem()->getQuantity() - 1)->update();
                }
            } catch (EntityException $e) {

            }

        } else {
            $order = new MoneyOrder();
            $order->setId($id)->fetch();

            $order->setStatus(\ShopItemOrder::STATUS_PROCESSED)
                  ->setDateProcessed(time());

            try {
                $order->update();
            } catch (EntityException $e) {

            }
        }
        $this->redirect('/private/monetisation');        
    }
}
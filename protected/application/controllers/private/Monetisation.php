<?php
namespace controllers\admin;

use \Application, \PrivateArea, \NewsModel, \Config, \ShopModel, \ChanceGame, \ChanceGamesModel, \EntityException;
use \ShopOrdersModel, \ShopItemOrder;

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

        $this->render('admin/monetisation', array(
            'title'      => 'Вывод средств',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list'       => $list,
        ));
    }

    public function approveAction($id)
    {
        $order = new ShopItemOrder();
        $order->setId($id)->fetch();
        
        $order->setStatus(\ShopItemOrder::STATUS_PROCESSED)
              ->setDateProcessed(time());

        try {
            $order->update();

            // substract points
            if (!$order->getChanceGameId()) {
                $order->getPlayer()->addPoints(-1*$order->getItem()->getPrice(), $order->getItem()->getTitle());
            }

            // update item
            if ($order->getItem()->getQuantity()) {
                $order->getItem()->setQuantity($order->getItem()->getQuantity() - 1)->update();
            }
        } catch (EntityException $e) {

        }

        $this->redirect('/private/monetisation');
    }
}
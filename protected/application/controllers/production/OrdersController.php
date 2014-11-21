<?php
namespace controllers\production;
use \Application, \Config, \Player, \EntityException, \Session, \MoneyOrder, \ShopItem, \ShopItemOrder;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class OrdersController extends \AjaxController
{
    public function init()
    {
        parent::init();
        if ($this->validRequest()) {
            if (!Session::connect()->get(Player::IDENTITY) instanceof PLayer) {
                $this->ajaxResponse(array(), 0, 'NOT_AUTHORIZED');
            }    
            Session::connect()->get(Player::IDENTITY)->markOnline();
        }
    }

    public function orderItemAction()
    {
        try {
            $item = new ShopItem();
            $item->setId($this->request()->post('itemId'))->fetch();    
        } catch (EntityException $e) {
            $this->ajaxResponse(array(), 0, $e->getMessage());
        }
        
        $order = new ShopItemOrder();
        $order->setPlayer(Session::connect()->get(Player::IDENTITY))
              ->setItem($item)
              ->setName($this->request()->post('name'))
              ->setSurname($this->request()->post('surname'))
              ->setPhone($this->request()->post('phone'))
              ->setRegion($this->request()->post('region'))
              ->setCity($this->request()->post('city'))
              ->setAddress($this->request()->post('addr'));

        if ($this->request()->post('chanceWin')) {
            try {
                $chanceWinData = ChanceGamesModel::instance()->getUnorderedChanceWinData($item->getId(), $order->getPlayer());    
            } catch (ModelException $e) {}

            if ($chanceWinData && $chanceWinData['Id']) {
                $order->setChanceGameId($chanceWinData['Id']);   
            }
        }

        try {
            $order->create();
        } catch(EntityException $e) {
            $this->ajaxResponse(array(), 0, $e->getMessage());
        }

        $this->ajaxResponse(array(
            'orderId'   => $order->getId(),
        ));
    }

    public function orderMoneyAction() 
    {
        $data = $this->request()->post('data');
        if (is_array($data)) {
            $order = new MoneyOrder();
            $order->setPlayer(Session::connect()->get(Player::IDENTITY))
                  ->setType($data['type']);
            unset($data['type']);

            $order->setData($data);
            try {
                $order->create();
            } catch(EntityException $e) {
                $this->ajaxResponse(array(), 0, $e->getMessage());
            }
        } else {
            $this->ajaxResponse(array(), 0, 'FRAUD');
        }

        $this->ajaxResponse(array(
            'orderId'   => $order->getId(),
        ));
    }
}
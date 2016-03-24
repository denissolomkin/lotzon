<?php
namespace controllers\production;
use \Application, \Player, \EntityException, \MoneyOrder, \ShopItem, \ShopItemOrder,\ChanceGamesModel, \ModelException;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class OrdersController extends \AjaxController
{
    public function init()
    {
        $this->session = new Session();
        parent::init();
        if ($this->validRequest()) {
//            if (!Session2::connect()->get(Player::IDENTITY) instanceof PLayer) {
            if (!$this->session->get(Player::IDENTITY) instanceof Player) {
                $this->ajaxResponse(array(), 0, 'NOT_AUTHORIZED');
            }    
//            Session2::connect()->get(Player::IDENTITY)->markOnline();
            $this->session->get(Player::IDENTITY)->markOnline();

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
        $number=preg_replace("/\D/","",$this->request()->post('phone'));
        if($number[0]==0)
            $number='38'.$number;

        $order = new ShopItemOrder();
        $order->setPlayer($this->session->get(Player::IDENTITY))
              ->setItem($item)
              ->setName($this->request()->post('name'))
              ->setSurname($this->request()->post('surname'))
              ->setNumber($number)
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

            if (!$order->getChanceGameId()) {
                $order->getPlayer()->addPoints(-1*$order->getItem()->getPrice(), $order->getItem()->getTitle());
            }

            if ($order->getItem()->getQuantity()) {
                $order->getItem()->setQuantity($order->getItem()->getQuantity() - 1)->update();
            }

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
//            $order->setPlayer(Session2::connect()->get(Player::IDENTITY))
            $order->setPlayer($this->session->get(Player::IDENTITY))
                  ->setType($data['type']);
            unset($data['type']);

            $order->setData($data);
            try {
                $order->beginTransaction();
                $order->create();
                // substract player money
                $sum = $order->getData()['summ']['value'];
                $order->getPlayer()->addMoney(-1*$sum, $order->getText());
                $order->commit();
            } catch(EntityException $e) {
                $order->rollBack();
                $this->ajaxResponse(array(), 0, $e->getMessage());
            }
        } else {
            $this->ajaxResponse(array(), 0, 'FRAUD');
        }

        $this->ajaxResponse(array(
            'orderId'   => $order->getId(),
        ));
    }

    public function convertAction()
    {
        $sum = $this->request()->post('sum');

        $player = new Player;
        $player->setId($this->session->get(Player::IDENTITY)->getId());

        $order = new MoneyOrder();
        $order->setPlayer($this->session->get(Player::IDENTITY))
            ->setType('points');

        $data = array(
            "summ" => array(
                "title" => "summ",
                "value" => $sum
            )
        );

        $order->setData($data);
        try {
            $order->beginTransaction();
            $money = \PlayersModel::instance()->getBalance($player, true)['Money'];
            if ($money<$sum) {
                throw new \Exception();
            }
            $order->create();
            $sum = $order->getData()['summ']['value'];
            $order->getPlayer()->addMoney(-1*$sum, $order->getText());
            $order->commit();
        } catch(EntityException $e) {
            $order->rollBack();
            $res = array(
                "message" => $e->getMessage(),
                "res"     => array()
            );
            $this->ajaxResponseNoCache($res, 402);
        } catch (\Exception $e) {
            \TicketsModel::instance()->rollBack();
            $res = array(
                "message" => "MONEY_NO_ENOUGH",
                "res"     => array()
            );
            $this->ajaxResponseNoCache($res, 402);
        }

        $player->fetch();
        $res = array(
            "player" => array(
                "balance" => array(
                    "money"  => $player->getMoney(),
                    "points" => $player->getPoints(),
                )
            )
        );

        $this->ajaxResponseNoCache($res);
    }

    public function cashoutAction()
    {
        $sum    = $this->request()->post('sum');
        $method = $this->request()->post('method');

        $player = new Player;
        $player->setId($this->session->get(Player::IDENTITY)->getId())->fetch();

        $order = new MoneyOrder();
        $order->setPlayer($this->session->get(Player::IDENTITY))
            ->setType($method);

        switch ($method) {
            case \MoneyOrder::GATEWAY_PHONE:
                $number = $player->getPhone();
                break;
            case \MoneyOrder::GATEWAY_QIWI:
                $number = $player->getQiwi();
                $this->ajaxResponseBadRequest("SELECT_OTHER_METHOD");
                break;
            case \MoneyOrder::GATEWAY_WEBMONEY:
                $number = $player->getWebMoney();
                break;
            case \MoneyOrder::GATEWAY_YANDEX:
                $number = $player->getYandexMoney();
                break;
            default:
                $this->ajaxResponseBadRequest("INVALID_PAYMENT_GATEWAY");
        }


        $data = array(
            $method => array(
                "title" => $method,
                "value" => $number
            ),
            "summ" => array(
                "title" => "Сумма",
                "value" => $sum
            )
        );

        $order->setData($data);
        try {
            $order->beginTransaction();
            $money = \PlayersModel::instance()->getBalance($player, true)['Money'];
            if ($money<$sum) {
                throw new \Exception();
            }
            $order->create();
            $sum = $order->getData()['summ']['value'];
            $order->getPlayer()->addMoney(-1*$sum, $order->getText());
            $order->commit();
        } catch(EntityException $e) {
            $order->rollBack();
            $res = array(
                "message" => $e->getMessage(),
                "res"     => array()
            );
            $this->ajaxResponseNoCache($res, 402);
        } catch (\Exception $e) {
            \TicketsModel::instance()->rollBack();
            $res = array(
                "message" => "MONEY_NO_ENOUGH",
                "res"     => array()
            );
            $this->ajaxResponseNoCache($res, 402);
        }


        $player->fetch();
        $res = array(
            "message" => "message-cashout-success",
            "player"  => array(
                "balance" => array(
                    "money"  => $player->getMoney(),
                    "points" => $player->getPoints(),
                )
            )
        );

        $this->ajaxResponseNoCache($res);
    }
}
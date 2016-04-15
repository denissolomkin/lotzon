<?php
namespace controllers\production;
use \Application, \Player, \EntityException, \MoneyOrder, \ShopItem, \ShopItemOrder,\ChanceGamesModel, \ModelException;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class OrdersController extends \AjaxController
{
    public function init()
    {
        parent::init();
        $this->validateRequest();
        $this->authorizedOnly();
        $this->validateCaptcha();
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
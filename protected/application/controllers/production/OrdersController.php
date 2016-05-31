<?php
namespace controllers\production;
use \Application, \EntityException, \MoneyOrder, \CountriesModel, \Player;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class OrdersController extends \AjaxController
{
    public function init()
    {
        parent::init();
        $this->validateRequest();
        $this->authorizedOnly(true);
        $this->validateLogout();
        $this->validateCaptcha();
    }

    public function convertAction()
    {
        $this->player->fetch();

        $sum  = $this->request()->post('sum');
        $currency = CountriesModel::instance()->getCountry($this->player->getCurrency())->loadCurrency();

        $data = array(
            'summ' => array(
                'title' => 'summ',
                'value' => $sum
            )
        );

        $order = new MoneyOrder();
        $order->setPlayer($this->player)
            ->setType('points')
            ->setText('Конвертация денег')
            ->setData($data)
            ->setCurrency($currency->getCode())
            ->setSum($sum)
            ->setEquivalent($sum / $currency->getCoefficient())
            ->setStatus(1);

        try {

            $order->beginTransaction();
            $order->create();
            $this->player
                ->addMoney(-1 * $sum, $order->getText())
                ->addPoints((int)(round($sum, 2) * $currency->getRate()), 'Обмен денег на баллы');
            $this->player->updateSession();
            $order->commit();

        } catch(EntityException $e) {

            $order->rollBack();
            $res = array(
                'message' => $e->getMessage(),
                'res'     => array()
            );
            $this->ajaxResponseNoCache($res, 402);
        }

        $res = array(
            'message' => 'message-convert-success',
            'player' => array(
                'balance' => array(
                    'money'  => $this->player->getMoney(),
                    'points' => $this->player->getPoints(),
                )
            )
        );

        $this->ajaxResponseNoCache($res);
    }

    public function cashoutAction()
    {

        $this->player
            ->fetch()
            ->initAccounts();

        $sum    = $this->request()->post('sum');
        $method = $this->request()->post('method');
        $number = $this->request()->post('number', false);

        switch ($method) {
            case \MoneyOrder::GATEWAY_PHONE:
                if (!in_array($this->player->getCountry(), array('RU', 'UA')))
                    $this->ajaxResponseBadRequest('COUNTRY_PHONE_UNAVAILABLE');
                $accountName = 'Phone';
                break;

            case \MoneyOrder::GATEWAY_QIWI:
                $this->ajaxResponseBadRequest('SELECT_OTHER_METHOD');
                $accountName = 'Qiwi';
                break;

            case \MoneyOrder::GATEWAY_WEBMONEY:
                $accountName = 'WebMoney';
                break;

            case \MoneyOrder::GATEWAY_YANDEX:
                $accountName = 'YandexMoney';
                break;

            default:
                $this->ajaxResponseBadRequest('INVALID_PAYMENT_GATEWAY');
                break;
        }

        switch (true) {

            case !$this->player->getAccounts($accountName):
                $this->ajaxResponseBadRequest('FIRST_FILL_ACCOUNT');
                break;

            case $number && !in_array($number, $this->player->getAccounts($accountName)):
                $this->ajaxResponseBadRequest('INVALID_ACCOUNT_NUMBER');
                break;

            case !$number && $this->player->getAccounts($accountName):
                $number = $this->player->getAccounts($accountName)[0];
                break;

            default:
                break;
        }

        $currency = CountriesModel::instance()->getCountry($this->player->getCurrency())->loadCurrency();
        $data = array(
            $method => array(
                'title' => $method,
                'value' => $number
            ),
            'summ' => array(
                'title' => 'Сумма',
                'value' => $sum
            )
        );

        $order = new MoneyOrder();
        $order->setPlayer($this->player)
            ->setType($method)
            ->setData($data)
            ->setCurrency($currency->getCode())
            ->setSum($sum)
            ->setEquivalent($sum / $currency->getCoefficient())
            ->setNumber(preg_replace("/\D/",'', $number));

        try {

            $order->beginTransaction();
            $order->create();
            $this->player->addMoney(-1 * $sum, $order->getText());
            $this->player->updateSession();
            $order->commit();

        } catch(EntityException $e) {

            $order->rollBack();
            $res = array(
                'message' => $e->getMessage(),
                'res'     => array()
            );

            $this->ajaxResponseNoCache($res, 402);

        }

        $res = array(
            'message' => 'message-cashout-success',
            'player'  => array(
                'balance' => array(
                    'money'  => $this->player->getMoney(),
                    'points' => $this->player->getPoints(),
                )
            )
        );

        $this->ajaxResponseNoCache($res);
    }

    public function statusAction($orderId)
    {
        $status = $this->request()->post('status', false);
        if (($status != '1')and($status != '2')) {
            $this->ajaxResponseBadRequest();
            return false;
        }
        try {
            $order = new MoneyOrder();
            $order->setId($orderId)->fetch();
            if ($order->getViewed() != 0) {
                $this->ajaxResponseBadRequest();
                return false;
            }
            if ($order->getPlayer()->getId() != $this->player->getId()) {
                $this->ajaxResponseBadRequest();
                return false;
            }
            $order->setViewed($status)->setViewedDate(time())->update();
            if ($status == 2) {
                $gift = new \Gift();
                $gift->setPlayerId($this->player->getId())->setObjectType('Ticket')->setObjectId(7)->setExpiryDate(\LotterySettingsModel::instance()->loadSettings()->getNearestGame()+strtotime('00:00:00', time()))->setUsed(false)->create();
            }
        } catch(EntityException $e) {
            $this->ajaxResponseBadRequest();
        }

        if ($status==2) {
            $res = array(
                'tickets' => array(
                    'filledTickets' => \TicketsModel::instance()->getUnplayedTickets($this->player->getId())
                )
            );
        } else {
            $res = array();
        }

        $this->ajaxResponseNoCache($res);
        return true;
    }
}

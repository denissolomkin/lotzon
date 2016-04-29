<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class MoneyOrder extends Entity 
{
    const STATUS_ORDERED = 0;
    const STATUS_PROCESSED = 1;
    const STATUS_DENIED = 2;

    const FOR_UPDATE = true;

    const GATEWAY_ITEM     = 'item';
    const GATEWAY_PHONE    = 'phone';
    const GATEWAY_QIWI     = 'qiwi';
    const GATEWAY_WEBMONEY = 'webmoney';
    const GATEWAY_YANDEX   = 'yandex';
    const GATEWAY_P24      = 'private24';
    const GATEWAY_POINTS   = 'points';

    protected $_id = 0;
    protected $_type = '';
    protected $_text = 'Вывод денег';
    protected $_status = self::STATUS_ORDERED;
    protected $_player = null;

    protected $_adminId = 0;
    protected $_adminName = '';
    protected $_count = 0;

    protected $_dateOrdered = 0;
    protected $_dateProcessed = 0;

    protected $_number = '';
    protected $_currency = '';
    protected $_sum = null;
    protected $_equivalent = null;
    protected $_data = array();

    protected $_item = null;

    public function init() 
    {
        $this->setModelClass('MoneyOrderModel');
    }

    public function setPlayer(Player $player) 
    {
        $player->initStats()->initCounters()->initDates();
        $this->_player = $player;

        return $this;
    }

    public function validate($action, $params = array()) 
    {
        switch($action) {
            case 'create' :

                switch (true) {

                    case !$this->getPlayer():
                        throw new EntityException("INVALID_PLAYER", 400);
                        break;

                    case !$this->getSum():
                        throw new EntityException("EMPTY_SUMM", 400);
                        break;

                    case !is_numeric($this->getSum()):
                        throw new EntityException("INVALID_SUMM", 400);
                        break;

                    case $this->getSum() <= 0:
                        throw new EntityException("INVALID_SUMM", 400);
                        break;

                    case $this->getType() != self::GATEWAY_POINTS
                        && SettingsModel::instance()->getSettings('counters')->getValue('MIN_MONEY_OUTPUT')
                        && $this->getSum() < SettingsModel::instance()->getSettings('counters')->getValue('MIN_MONEY_OUTPUT'):
                        throw new EntityException("INVALID_MIN_OUTPUT", 400);
                        break;

                    case $this->getSum() > $this->getPlayer()->getBalance(self::FOR_UPDATE)['Money']:
                        throw new EntityException("INSUFFICIENT_FUNDS", 400);
                        break;
                }

                /*
                $data = $this->getData();
                switch ($this->getType()) {


                    case self::GATEWAY_P24:
                        if (empty($data['card-number']['value'])) {
                            throw new EntityException("EMPTY_CARD_NUMBER", 400);
                        }
                        if (empty($data['name']['value'])) {
                            throw new EntityException("EMPTY_CREDENTIALS", 400);
                        }
                        $data['name']['value'] = htmlspecialchars(strip_tags($data['name']['value']));
                         // clean up card number
                        $cardNumber = preg_replace("/[^0-9]/", "", $data['card-number']['value']);
                        // verify visa or mastercard
                        if (!preg_match("/^((4[0-9]{12}(?:[0-9]{3}))|(5[1-5][0-9]{14}))$/", $cardNumber)) {
                            throw new EntityException("INVALID_CARD_NUMBER", 400);
                        }
                        $this->setNumber($cardNumber);
                    break;

                    case self::GATEWAY_CARD:
                        // clean up card number
                        if (empty($data['number']['value'])) {
                            throw new EntityException("EMPTY_CARD_NUMBER", 400);
                        }
                        $cardNumber = preg_replace("/[^0-9]/", "", $data['number']['value']);
                        // verify visa or mastercard
                        if (!preg_match("/^((4[0-9]{12}(?:[0-9]{3}))|(5[1-5][0-9]{14}))$/", $cardNumber)) {
                            throw new EntityException("INVALID_CARD_NUMBER", 400);
                        }
                        if (empty($data['name']['value'])) {
                            throw new EntityException("EMPTY_CREDENTIALS", 400);
                        }
                        $data['name']['value'] = htmlspecialchars(strip_tags($data['name']['value']));
                    break;

                    case self::GATEWAY_QIWI:
                        $number = $this->getPlayer()->getQiwi();
                        if (!$number) {
                            throw new EntityException("EMPTY_QIWI", 400);
                        }
                        if (!preg_match('/^[+0-9\- ()]*$/', $number)) {
                            throw new EntityException("INVALID_QIWI_FORMAT", 400);
                        }
                        $number = preg_replace("/[^0-9]/", "", $number);
                        $this->setNumber(($number[0]==0?'38':'').$number);
                    break;


                    case self::GATEWAY_PHONE:
                        $number = $this->getPlayer()->getPhone();
                        if (!$number) {
                            throw new EntityException("EMPTY_PHONE", 400);
                        }
                        if (!preg_match('/^[+0-9\- ()]*$/', $number)) {
                            throw new EntityException("INVALID_PHONE_FORMAT", 400);
                        }
                        $number = preg_replace("/[^0-9]/", "", $number);
                        $this->setNumber(($number[0] == 0 ? '38' : '') . $number);
                        break;


                    case self::GATEWAY_WEBMONEY:
                        $number = $this->getPlayer()->getWebMoney();
                        if (!$number) {
                            throw new EntityException("EMPTY_WEBMONEY_PURSE", 400);
                        }
                        if (!preg_match("/[a-z][0-9]{12}/i", $number)) {
                            throw new EntityException("INVALID_WEBMONEY_PURSE", 400);
                        }
                        $this->setNumber($number);
                        break;

                    case self::GATEWAY_YANDEX:
                        $number = $this->getPlayer()->getYandexMoney();
                        if (!$number) {
                            throw new EntityException("EMPTY_YANDEX_PURSE", 400);
                        }
                        if (!preg_match("/[0-9]+/", $number)) {
                            throw new EntityException("INVALID_YANDEX_PURSE", 400);
                        }
                        $this->setNumber($number);
                        break;

                    case self::GATEWAY_ITEM:

                        $number = $this->getPlayer()->getPhone();
                        if (!$number) {
                            throw new EntityException("EMPTY_PHONE", 400);
                        }

                        if (!preg_match('/^[+0-9\- ()]*$/', $number)) {
                            throw new EntityException("INVALID_PHONE_FORMAT", 400);
                        }

                        $number = preg_replace("/[^0-9]/", "", $number);
                        $this->setNumber(($number[0] == 0 ? '38' : '') . $number);

                        $item = new ShopItem();
                        $item->setId($data['item']['value']);

                        try {
                            $this->setItem($item->fetch());
                        } catch (EntityException $e) {
                            throw new EntityException("INVALID_ITEM", 400);
                        }

                        $data['item']['title'] = $item->getTitle($this->getPlayer()->getCountry());
                        $data['summ'] = array('title' => 'Сумма', 'value' => $item->getSum());

                        break;

                    case self::GATEWAY_POINTS:
                        $rate = CountriesModel::instance()->getCountry($this->getPlayer()->getCountry())->loadCurrency()->getRate();
                        $this->setStatus(1)
                            ->setText('Конвертация денег');
                        $this->getPlayer()
                            ->addPoints((int)(round($this->getData()['summ']['value'], 2) * $rate), "Обмен денег на баллы");
                        break;

                    default:
                        throw new EntityException("INVALID_PAYMENT_GATEWAY", 400);
                        break;
                }*/

                // set sum in universal currency for statistics

                return true;
                break;

            case 'update' :
                break;

            case 'delete' :
                break;

            case 'fetch' :
                break;

            default :
                throw new EntityException("Object validation fails", 400);
                break;
        }

        return $this;
    }

    public function formatFrom($from, $data) 
    {
        if ($from == 'DB') {

            $this->setId($data['Id'])
                 ->setDateOrdered($data['DateOrdered'])
                 ->setDateProcessed($data['DateProcessed'])
                 ->setStatus($data['Status'])
                 ->setAdminId($data['AdminId'])
                 ->setAdminName($data['AdminName'])
                 ->setNumber($data['Number'])
                 ->setCurrency($data['Currency'])
                 ->setSum($data['Sum'])
                 ->setEquivalent($data['Equivalent'])
                 ->setType($data['Type'])
                 ->setData(@unserialize($data['Data']));

            if($data['ItemId']) {
                $item = new ShopItem();
                $item->setId($data['ItemId']);

                try {
                    $this->setItem($item->fetch());
                } catch (EntityException $e) {
                    $this->setItem(new ShopItem());
                }
            }

            if($data['PlayerId']){
                $player = new Player();
                $player->setId($data['PlayerId']);
                $this->setPlayer($player->fetch())
                    ->setCount($data['Count']);
            }
        }

        return $this;
    }
}
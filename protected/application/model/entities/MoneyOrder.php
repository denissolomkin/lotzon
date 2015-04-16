<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class MoneyOrder extends Entity 
{
    const STATUS_ORDERED = 0;
    const STATUS_PROCESSED = 1;

    const FOR_UPDATE = true;
    
    const GATEWAY_PHONE    = 'phone';
    const GATEWAY_QIWI     = 'qiwi';
    const GATEWAY_WEBMONEY = 'webmoney';
    const GATEWAY_YANDEX   = 'yandex';
    const GATEWAY_P24      = 'private24';
    const GATEWAY_POINTS   = 'points';

    private $_id       = 0;
    private $_type     = '';
    private $_text     = 'Вывод денег';
    private $_player   = null;
    private $_userid   = 0;
    private $_username   = '';
    private $_dateOrdered    = '';
    private $_dateProcessed  = '';
    private $_number       = '';
    private $_count       = 0;
    private $_adminProcessed = '';
    private $_status   = self::STATUS_ORDERED;

    private $_orderData = array();

    public function init() 
    {
        $this->setModelClass('MoneyOrderModel');
    }

    public function setId($id)
    {
        $this->_id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setUserId($userid)
    {
        $this->_userid = $userid;

        return $this;
    }

    public function getUserId()
    {
        return $this->_userid;
    }

    public function setUserName($username)
    {
        $this->_username = $username;

        return $this;
    }

    public function getUserName()
    {
        return $this->_username;
    }

    public function setCount($count)
    {
        $this->_count = $count;

        return $this;
    }

    public function getCount()
    {
        return $this->_count;
    }

    public function setText($text)
    {
        $this->_text = $text;

        return $this;
    }

    public function getText()
    {
        return $this->_text;
    }

    public function setType($type)
    {
        $this->_type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function setPlayer(Player $player) 
    {
        $player->initDates()->initCounters();
        $this->_player = $player;

        return $this;
    }

    public function getPlayer()
    {
        return $this->_player;
    }

    public function setDateOrdered($dateOrdered)
    {
        $this->_dateOrdered = $dateOrdered;

        return $this;
    }

    public function getDateOrdered()
    {
        return $this->_dateOrdered;
    }

    public function setDateProcessed($dateProcessed)
    {
        $this->_dateProcessed = $dateProcessed;

        return $this;
    }

    public function getDateProcessed()
    {
        return $this->_dateProcessed;
    }

    public function setStatus($status)
    {
        $this->_status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function setNumber($number)
    {
        $this->_number = preg_replace("/\D/","",$number);

        return $this;
    }

    public function getNumber()
    {
        return $this->_number;
    }

    public function setData($orderData)
    {
        $this->_orderData = $orderData;

        return $this;
    }

    public function getData()
    {
        return $this->_orderData;
    }


    public function validate($action, $params = array()) 
    {
        switch($action) {
            case 'create' :
                if (!$this->getPlayer()) {
                    throw new EntityException("INVALID_PLAYER", 400);
                }
                if (!in_array($this->getType(), array(self::GATEWAY_PHONE, self::GATEWAY_QIWI, self::GATEWAY_WEBMONEY, self::GATEWAY_YANDEX, self::GATEWAY_P24, self::GATEWAY_POINTS))) {
                    throw new EntityException("INVALID_PAYMENT_GATEWAY", 400);
                }
                if (!$this->getData()['summ']['value']) {
                    throw new EntityException("EMPTY_SUMM", 400);   
                }
                if (!is_numeric($this->getData()['summ']['value']) || $this->getData()['summ']['value'] <= 0 ) {
                    throw new EntityException("INVALID_SUMM", 400);
                }
                if ($this->getData()['summ']['value'] > $this->getPlayer()->getBalance(self::FOR_UPDATE)['Money']) {
                    throw new EntityException("INSUFFICIENT_FUNDS", 400);
                }
                $data = $this->getData();
                switch ($this->getType()) {
                    /*case self::GATEWAY_CARD:
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
                    break;*/
                    case self::GATEWAY_PHONE:
                        $number = $this->getPlayer()->getPhone();
                        if (!$number) {
                            throw new EntityException("EMPTY_PHONE", 400);
                        }
                        if (!preg_match('/^[+0-9\- ()]*$/', $number)) {
                            throw new EntityException("INVALID_PHONE_FORMAT", 400);
                        }
                        $number = preg_replace("/[^0-9]/", "", $number);
                        $this->setNumber(($number[0]==0?'38':'').$number);
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
                    case self::GATEWAY_POINTS:
                        $rate=CountriesModel::instance()->getCountry($this->getPlayer()->getCountry())->loadCurrency()->getRate();
                        $this->setStatus(1)
                            ->setText('Конвертация денег')
                            ->getPlayer()
                            ->addPoints((int)(round($this->getData()['summ']['value'],2)*$rate), "Обмен денег на баллы");
                        break;
                }
                $this->setData($data);
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
                 ->setUserName($data['UserName'])
                 ->setNumber($data['Number'])
                 ->setType($data['Type'])
                 ->setData(@unserialize($data['Data']));

            if($data['PlayerId']){
                $player = new Player();
                $player->setId($data['PlayerId']);
                $this->setPlayer($player->fetch())
                    ->setCount($data['Count']);
            }
        }

        return $this;
    }

    public function beginTransaction()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->beginTransaction();
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function commit()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->commit();
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function rollBack()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->rollBack();
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }    
}
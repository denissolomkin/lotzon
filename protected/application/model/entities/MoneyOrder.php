<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class MoneyOrder extends Entity 
{
    const STATUS_ORDERED = 0;
    const STATUS_PROCESSED = 1;
    
    const GATEWAY_CARD     = 'card';
    const GATEWAY_QIWI     = 'qiwi';
    const GATEWAY_WEBMONEY = 'webmoney';
    const GATEWAY_YANDEX   = 'yandex';
    const GATEWAY_P24      = 'private24';

    private $_id       = 0;
    private $_type     = '';
    private $_player   = null;
    private $_dateOrdered    = '';
    private $_dateProcessed  = '';
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
                if (!in_array($this->getType(), array(self::GATEWAY_CARD, self::GATEWAY_QIWI, self::GATEWAY_WEBMONEY, self::GATEWAY_YANDEX, self::GATEWAY_P24))) {
                    throw new EntityException("INVALID_PAYMENT_GATEWAY", 400);
                }
                if (!$this->getData()['summ']['value']) {
                    throw new EntityException("EMPTY_SUMM", 400);   
                }
                if (!is_numeric($this->getData()['summ']['value'])) {
                    throw new EntityException("INVALID_SUMM", 400);
                }
                
                if ($this->getData()['summ']['value'] > $this->getPlayer()->getMoney()) {
                    throw new EntityException("INSUFFICIENT_FUNDS", 400);   
                }

                $data = $this->getData();
                switch ($this->getType()) {
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
                        if (empty($data['phone']['value'])) {
                            throw new EntityException("EMPTY_PHONE", 400);   
                        }
                        if (!preg_match('/^[+0-9\- ()]*$/', $data['phone']['value'])) {
                            throw new EntityException("INVALID_PHONE_FORMAT", 400);
                        }
                    break;
                    case self::GATEWAY_WEBMONEY:
                        if (empty($data['purse']['value'])) {
                            throw new EntityException("EMPTY_WEBMONEY_CURRENCY", 400);       
                        }
                        if (empty($data['card-number']['value'])) {
                            throw new EntityException("EMPTY_WEBMONEY_PURSE", 400);          
                        }
                        if (!in_array(strtolower($data['purse']['value']), array('wmu','wmr','wmb'))) {
                            throw new EntityException("INVALID_WEBMONEY_CURRENCY", 400);
                        }
                        if (!preg_match("/[a-z][0-9]{12}/i", $data['card-number']['value'])) {
                            throw new EntityException("INVALID_WEBMONEY_PURSE", 400);   
                        }
                    break;
                    case self::GATEWAY_YANDEX:
                        if (empty($data['card-number']['value'])) {
                            throw new EntityException("EMPTY_YANDEX_PURSE", 400);
                        }
                        if (!preg_match("/[0-9]+/", $data['card-number']['value'])) {
                            throw new EntityException("INVALID_YANDEX_PURSE", 400);   
                        }
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
            $player = new Player();
            $player->setId($data['PlayerId']);

            $this->setId($data['Id'])
                 ->setPlayer($player->fetch())
                 ->setDateOrdered($data['DateOrdered'])
                 ->setDateProcessed($data['DateProcessed'])
                 ->setStatus($data['Status'])
                 ->setType($data['Type'])
                 ->setData(@unserialize($data['Data']));
        }

        return $this;
    }
}
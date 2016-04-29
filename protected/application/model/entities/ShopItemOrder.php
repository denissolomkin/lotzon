<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class ShopItemOrder extends Entity 
{
    const STATUS_ORDERED = 0;
    const STATUS_PROCESSED = 1;
    const STATUS_DENIED = 2;

    const FOR_UPDATE = true;

    protected $_id      = 0;
    protected $_item    = null;
    protected $_status  = self::STATUS_ORDERED;
    protected $_player  = null;

    protected $_adminId   = 0;
    protected $_adminName = '';
    protected $_count     = 0;

    protected $_dateOrdered   = '';
    protected $_dateProcessed = '';

    protected $_name       = '';
    protected $_surname    = '';
    protected $_secondName = '';
    protected $_phone      = '';
    protected $_region     = '';
    protected $_city       = '';
    protected $_address    = '';

    protected $_number  = null;
    protected $_sum = null;
    protected $_equivalent = null;

    protected $_chanceGameId = 0;

    public function init() 
    {
        $this->setModelClass('ShopOrdersModel');
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
                if (!$this->getPlayer()) {
                    throw new EntityException("INVALID_PLAYER", 400);
                }
                if (!$this->getItem()) {
                    throw new EntityException("INVALID_ITEM", 400);
                }
                if (!$this->getName()) {
                    throw new EntityException("ORDER_INVALID_NAME", 400);
                }
                if (!$this->getSurname()) {
                    throw new EntityException("ORDER_INVALID_SURNAME", 400);
                }
                if (!$this->getPhone()) {
                    throw new EntityException("ORDER_INVALID_PHONE", 400);
                }
                /*
                if ($this->getPhone() && !preg_match('/^[+0-9\- ()]*$/', $this->getPhone())) {
                    throw new EntityException("INVALID_PHONE_FORMAT", 400);
                }
                */
                if (!$this->getCity()) {
                    throw new EntityException("ORDER_INVALID_CITY", 400);
                }
                if (!$this->getAddress()) {
                    throw new EntityException("ORDER_INVALID_ADDRESS", 400);
                }
                if (!$this->getChanceGameId() && $this->getPlayer()->getBalance(self::FOR_UPDATE)['Points'] < $this->getSum()) {
                    throw new EntityException("POINTS_NOT_ENOUGH", 400);
                }

                $this->setName(htmlspecialchars(strip_tags($this->getName())));
                $this->setSurname(htmlspecialchars(strip_tags($this->getSurname())));
                $this->setSecondName(htmlspecialchars(strip_tags($this->getSecondName())));
                $this->setCity(htmlspecialchars(strip_tags($this->getCity())));
                $this->setAddress(htmlspecialchars(strip_tags($this->getAddress())));
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
            $item = new ShopItem();
            $item->setId($data['ItemId']);

            if($data['PlayerId']) {
                $player = new Player();
                $player->setId($data['PlayerId']);
                $this->setPlayer($player->fetch())
                    ->setCount($data['Count']);;
            }

            $this->setId($data['Id'])
                 ->setDateOrdered($data['DateOrdered'])
                 ->setDateProcessed($data['DateProcessed'])
                 ->setStatus($data['Status'])
                 ->setNumber($data['Number'])
                 ->setSum($data['Sum'])
                 ->setAdminName($data['AdminName'])
                 ->setAdminId($data['AdminId'])
                 ->setName($data['Name'])
                 ->setSurname($data['Surname'])
                 ->setSecondName($data['SecondName'])
                 ->setPhone($data['Phone'])
                 ->setRegion($data['Region'])
                 ->setCity($data['City'])
                 ->setAddress($data['Address'])
                 ->setChanceGameId($data['ChanceGameId']);

            try {
                $this->setItem($item->fetch());
            } catch (EntityException $e) {
                $this->setItem(new ShopItem());
            }
        }

        return $this;
    }
}
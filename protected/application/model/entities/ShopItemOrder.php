<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class ShopItemOrder extends Entity 
{
    const STATUS_ORDERED = 0;
    const STATUS_PROCESSED = 1;

    private $_id       = 0;
    private $_item     = null;
    private $_player   = null;
    private $_number   = null;
    private $_userid   = 0;
    private $_username   = '';
    private $_dateOrdered    = '';
    private $_dateProcessed  = '';
    private $_adminProcessed = '';
    private $_status   = self::STATUS_ORDERED;


    private $_name       = '';
    private $_surname    = '';
    private $_secondName = '';
    private $_phone      = '';

    private $_region     = '';
    private $_city       = '';
    private $_addr       = '';

    private $_chanceGameId = 0;

    public function init() 
    {
        $this->setModelClass('ShopOrdersModel');
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

    public function setNumber($number)
    {
        $this->_number = $number;

        return $this;
    }

    public function getNumber()
    {
        return $this->_number;
    }

    public function setChanceGameId($chanceGameId)
    {
        $this->_chanceGameId = $chanceGameId;

        return $this;
    }

    public function getChanceGameId()
    {
        return $this->_chanceGameId;
    }

    public function setItem(ShopItem $item) 
    {
        $this->_item = $item;

        return $this;
    }

    public function getItem()
    {
        return $this->_item;
    }

    public function setPlayer(Player $player) 
    {
        $player->updateCounters();
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

    public function setName($name)
    {
        $this->_name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setSurname($surname)
    {
        $this->_surname = $surname;

        return $this;
    }

    public function getSurname()
    {
        return $this->_surname;
    }

    public function setSecondName($secondName)
    {
        $this->_secondName = $secondName;

        return $this;
    }

    public function getSecondName()
    {
        return $this->_secondName;
    }

    public function setPhone($phone)
    {
        $this->_phone = $phone;

        return $this;
    }

    public function getPhone()
    {
        return $this->_phone;
    } 

    public function setRegion($region)
    {
        $this->_region = $region;

        return $this;
    }

    public function getRegion()
    {
        return $this->_region;
    } 

    public function setCity($city)
    {
        $this->_city = $city;

        return $this;
    }

    public function getCity()
    {
        return $this->_city;
    } 

    public function setAddress($addr)
    {
        $this->_addr = $addr;

        return $this;
    }

    public function getAddress()
    {
        return $this->_addr;
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
                if ($this->getPhone() && !preg_match('/^[+0-9\- ()]*$/', $this->getPhone())) {
                    throw new EntityException("INVALID_PHONE_FORMAT", 400);
                }
                if (!$this->getCity()) {
                    throw new EntityException("ORDER_INVALID_CITY", 400);
                }
                if (!$this->getAddress()) {
                    throw new EntityException("ORDER_INVALID_ADRESS", 400);
                }

                if (!$this->getChanceGameId() && $this->getPlayer()->getPoints() < $this->getItem()->getPrice()) {
                    throw new EntityException("INSUFFICIENT_FUNDS", 400);   
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

            if($data['PlayerId']){
                $player = new Player();
                $player->setId($data['PlayerId']);
                $this->setPlayer($player->fetch());
            }

            $this->setId($data['Id'])
                 ->setDateOrdered($data['DateOrdered'])
                 ->setDateProcessed($data['DateProcessed'])
                 ->setStatus($data['Status'])
                 ->setNumber($data['Number'])
                 ->setUserName($data['UserName'])
                 ->setUserId($data['UserId'])
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
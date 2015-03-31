<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class Notice extends Entity
{
    private $_id    = '';
    private $_playerid = '';
    private $_userid = '';
    private $_username = '';
    private $_text  = '';
    private $_type  = '';
    private $_title  = '';
    private $_country  = null;
    private $_minLotteries  = null;
    private $_registeredFrom  = null;
    private $_registeredUntil = null;
    private $_date  = '';
    
    public function init()
    {
        $this->setModelClass('NoticesModel');
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

    public function setCountry($country)
    {
        $this->_country = $country;

        return $this;
    }

    public function getCountry()
    {
        return $this->_country;
    }

    public function setRegisteredFrom($from)
    {
        if(isset($from) && !is_numeric($from))
            $from=strtotime($from);

        $this->_registeredFrom = $from;

        return $this;
    }

    public function getRegisteredFrom()
    {
        return $this->_registeredFrom;
    }

    public function setRegisteredUntil($to)
    {
        if(isset($to) && !is_numeric($to))
            $to=strtotime($to);

        $this->_registeredUntil = $to;

        return $this;
    }

    public function getRegisteredUntil()
    {
        return $this->_registeredUntil;
    }

    public function setMinLotteries($int)
    {
        $this->_minLotteries = $int;

        return $this;
    }

    public function getMinLotteries()
    {
        return $this->_minLotteries;
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

    public function setUserId($userid) {
        $this->_userid = $userid;

        return $this;
    }

    public function getUserId()
    {
        return $this->_userid;
    }

    public function setUserName($user)
    {
        $this->_username = $user;

        return $this;
    }

    public function getUserName()
    {
        return $this->_username;
    }

    public function setPlayerId($playerid) {
        $this->_playerid = $playerid;

        return $this;
    }

    public function getPlayerId()
    {
        return $this->_playerid;
    }

    public function setTitle($title)
    {
        $this->_title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setDate($date) 
    {
        $this->_date = $date;

        return $this;
    }

    public function getDate()
    {
        return $this->_date;
    }

    public function formatFrom($from, $data) 
    {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                 ->setPlayerId($data['PlayerId'])
                 ->setDate($data['Date'])
                 ->setUserName($data['UserName'])
                 ->setTitle($data['Title'])
                 ->setText($data['Text'])
                ->setCountry($data['Country'])
                ->setMinLotteries($data['MinLotteries'])
                ->setRegisteredUntil($data['RegisteredUntil'])
                ->setRegisteredFrom($data['RegisteredFrom']);
        }

        return $this;
    }

    public function validate($action)
    {
        switch ($action) {
            case 'create' :
            case 'update' :
                if (!$this->getTitle()) {
                    throw new EntityException("Title can not be empty", 400);
                }
            break;
            
            case 'delete':
                if (!$this->getId()) {
                    throw new EntityException("Identifier can't be empty", 400);
                }                
            break;
            default:
                throw new EntityException('Object does not pass validation', 400);
            break;
        }
    }
}
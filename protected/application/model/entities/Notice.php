<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class Notice extends Entity
{
    protected $_id    = '';
    protected $_playerId = '';
    protected $_adminId = '';
    protected $_adminName = '';
    protected $_text  = '';
    protected $_type  = '';
    protected $_title  = '';
    protected $_country  = null;
    protected $_minLotteries  = null;
    protected $_registeredFrom  = null;
    protected $_registeredUntil = null;
    protected $_date  = '';
    
    public function init()
    {
        $this->setModelClass('NoticesModel');
    }

    public function setRegisteredFrom($from)
    {
        if(isset($from) && !is_numeric($from))
            $from=strtotime($from);

        $this->_registeredFrom = $from;

        return $this;
    }

    public function setRegisteredUntil($to)
    {
        if(isset($to) && !is_numeric($to))
            $to=strtotime($to);

        $this->_registeredUntil = $to;

        return $this;
    }

    public function formatFrom($from, $data) 
    {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                 ->setPlayerId($data['PlayerId'])
                 ->setDate($data['Date'])
                 ->setAdminName($data['AdminName'])
                 ->setAdminId($data['AdminId'])
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
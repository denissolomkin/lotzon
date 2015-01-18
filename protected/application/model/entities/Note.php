<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class Note extends Entity
{
    private $_id    = '';
    private $_playerid = '';
    private $_userid = '';
    private $_username = '';
    private $_text  = '';
    private $_date  = '';
    
    public function init()
    {
        $this->setModelClass('NotesModel');
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

    public function setText($text)
    {
        $this->_text = $text;

        return $this;
    }

    public function getText()
    {
        return $this->_text;
    }

    public function setPlayerId($playerid) {
        $this->_playerid = $playerid;

        return $this;
    }

    public function getPlayerId()
    {
        return $this->_playerid;
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

    public function setUserId($userid)
    {
        $this->_userid = $userid;

        return $this;
    }

    public function getUserId()
    {
        return $this->_userid;
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
                 ->setUserId($data['UserId'])
                 ->setUserName($data['UserName'])
                 ->setDate($data['Date'])
                 ->setText($data['Text']);
        }

        return $this;
    }

    public function validate($action)
    {
        switch ($action) {
            case 'create' :
            case 'update' :
                if (!$this->getText()) {
                    throw new EntityException("Text can not be empty", 400);
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
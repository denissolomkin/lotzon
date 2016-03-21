<?php

class Message extends Entity
{
    protected $_id          = 0;
    protected $_playerId    = 0;
    protected $_toPlayerId  = 0;
    protected $_playerImg   = '';
    protected $_playerName  = '';
    protected $_text        = '';
    protected $_image       = '';
    protected $_date        = 0;
    protected $_status      = 0;

    public function init()
    {
        $this->setModelClass('MessagesModel');
    }

    public function formatFrom($from, $data)
    {
        if ($from == 'DB') {
            foreach ($data as $key=>$value) {
                $method = 'set'.$key;
                $this->$method($value);
            }
        }
        return $this;
    }

    public function export($to)
    {
        switch ($to) {
            case 'list':
                $ret = array(
                    'user'         => array(
                        'id'   => $this->getPlayerId(),
                        'img'  => $this->getPlayerImg(),
                        'name' => $this->getPlayerName(),
                    ),
                    'id'           => $this->getId(),
                    'recipient_id' => $this->getToPlayerId(),
                    'date'         => $this->getDate(),
                    'text'         => $this->getText(),
                    'img'          => $this->getImage() === NULL ? "" : $this->getImage(),
                );
                break;
            case 'talk':
                $ret = array(
                    'user'         => array(
                        'id'   => $this->getPlayerId(),
                        'img'  => $this->getPlayerImg(),
                        'name' => $this->getPlayerName(),
                    ),
                    'id'           => $this->getPlayerId(),
                    'date'         => $this->getDate(),
                    'text'         => $this->getText(),
                    'img'          => $this->getImage() === NULL ? "" : $this->getImage(),
                );
                if ($this->getStatus()==0) {
                    $ret['isUnread'] = true;
                }
                break;
        }

        return $ret;
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
            case 'fetch' :
            case 'delete':
                if (!$this->getId()) {
                    throw new EntityException("Identifier can't be empty", 400);
                }
                break;
            default:
                throw new EntityException('Object does not pass validation', 400);
                break;
        }
        return true;
    }
}

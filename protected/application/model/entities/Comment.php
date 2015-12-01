<?php

class Comment extends Entity
{
    protected $_id          = 0;
    protected $_parentId    = 0;
    protected $_toPlayerId  = 0;
    protected $_playerId    = 0;
    protected $_playerImg   = '';
    protected $_playerName  = '';
    protected $_text        = '';
    protected $_image       = '';
    protected $_date        = 0;
    protected $_isPromo     = 0;
    protected $_status      = 0;
    protected $_adminId     = 0;
    protected $_module      = '';
    protected $_objectId    = 0;
    protected $_modifyDate  = 0;
    protected $_likesCount  = 0;

    public function init()
    {
        $this->setModelClass('CommentsModel');
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
        if ($to == 'JSON') {
            $ret = array(
                'user' => array(
                    'id'   => $this->getPlayerId(),
                    'img'  => $this->getPlayerImg(),
                    'name' => $this->getPlayerName(),
                ),
                'id'    => $this->getId(),
                'date'  => date('d.m.Y H:i',$this->getDate()),
                'text'  => $this->getText(),
                'likes' => $this->getLikesCount(),
                'img'   => $this->getImage()===NULL?"":$this->getImage(),
            );
            return $ret;
        }
    }

    public function validate($action)
    {
        switch ($action) {
            case 'create' :
                if ($this->getParentId()) {
                    $parent = new Comment;
                    $new_parent_id = $parent->setId($this->getParentId())->fetch()->getParentId();
                    if ($new_parent_id) {
                        $this->setParentId($new_parent_id);
                    }
                }
                break;
            case 'update' :
                $this->setText(htmlspecialchars(strip_tags($this->getText())));
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

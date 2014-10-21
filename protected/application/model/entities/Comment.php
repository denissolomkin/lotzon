<?php

class Comment extends Entity
{
    private $_id = 0;
    private $_author = 0;
    private $_link = '';
    private $_avatar = '';
    private $_text = '';
    private $_date = 0;

    public function init()
    {
        $this->setModelClass('CommentsModel');
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

    public function setAuthor($author) 
    {
        $this->_author = $author;

        return $this;
    }
    
    public function getAuthor()
    {
        return $this->_author;
    }

    public function setLink($link) 
    {
        $this->_link = $link;

        return $this;
    }
    
    public function getLink()
    {
        return $this->_link;
    }

    public function setAvatar($avatar) 
    {
        $this->_avatar = $avatar;

        return $this;
    }
    
    public function getAvatar()
    {
        return $this->_avatar;
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

    public function setDate($date) 
    {
        $this->_date = $date;

        return $this;
    }
    
    public function getDate()
    {
        return $this->_date;
    }

    public function validate()
    {
        return true;
    }
}
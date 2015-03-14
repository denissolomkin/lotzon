<?php

class OnlineGame extends Entity
{
    private $_id = 0;
    private $_key = '';
    private $_title = '';
    private $_description = '';
    private $_options = '';
    private $_prices = '';
    private $_enabled = true;
/*  private $_stackPlayers = 0;
    private $_players = 0;
    private $_maxPlayers = 0;
    private $_fieldSizeX = 0;
    private $_fieldSizeY = 0;
    private $_timeout = 0;
    private $_moves = 0;
    private $_maxPoints = 0;
    private $_prices = '';
    private $_botEnabled = true;
*/

    public function init()
    {
        $this->setModelClass('OnlineGamesModel');
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

    public function setKey($key)
    {
        $this->_key = $key;
        return $this;
    }

    public function getKey()
    {
        return $this->_key;
    }

    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    public function getTitle($lang=false)
    {
        if($lang) {
            if(isset($this->_title[$lang]) && $this->_title[$lang])
                $title = $this->_title[$lang];
            else
                $title = $this->_title[\Config::instance()->defaultLang];
        } else
            $title = $this->_title;

        return $title;
    }

    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    public function getDescription($lang=false)
    {
        if($lang) {
            if(isset($this->_description[$lang]) && $this->_description[$lang])
                $description = $this->_description[$lang];
            else
                $description = $this->_description[\Config::instance()->defaultLang];
        } else
            $description = $this->_description;

        return $description;
    }
/*
    public function setStackPlayers($stackPlayers)
    {
        $this->_stackPlayers = $stackPlayers;
        return $this;
    }

    public function getStackPlayers()
    {
        return $this->_stackPlayers;
    }

    public function setPlayers($int)
    {
        return $this->_players = (int )$int;
        return $this;
    }

    public function getPlayers()
    {
        return $this->_players;
    }

    public function setMaxPlayers($int)
    {
        $this->_maxPlayers = (int )$int;
        return $this;
    }

    public function getMaxPlayers()
    {
        return $this->_maxPlayers;
    }

    public function setFieldSizeX($int)
    {
        $this->_fieldSizeX = (int) $int;
        return $this;
    }

    public function getFieldSizeX()
    {
        return $this->_fieldSizeX;
    }

    public function setFieldSizeY($int)
    {
        $this->_fieldSizeY = (int) $int;
        return $this;
    }

    public function getFieldSizeY()
    {
        return $this->_fieldSizeY;
    }

    public function setTimeOut($int)
    {
        $this->_timeout = (int) $int;
        return $this;
    }

    public function getTimeOut()
    {
        return $this->_timeout;
    }

    public function setMoves($int)
    {
        $this->_moves = (int) $int;;
        return $this;
    }

    public function getMoves()
    {
        return $this->_moves;
    }

    public function setMaxPoints($int)
    {
        $this->_maxPoints = (int) $int;;
        return $this;
    }

    public function getMaxPoints()
    {
        return $this->_maxPoints;
    }

    public function setPrices($array)
    {
        $this->_prices=$array;
        return $this;
    }

    public function getPrices()
    {
        return $this->_prices;
    }

    public function setBotEnabled($bool)
    {
        $this->_botEnabled=(bool) $bool;
        return $this;
    }

    public function isBotEnabled()
    {
        return $this->_botEnabled;
    }
*/
    public function setOptions($array)
    {
        $this->_options=$array;
        return $this;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function getOption($key)
    {
        return isset($this->_options[$key]) ? $this->_options[$key] : false;
    }

    public function setModes($array)
    {
        $this->_modes=$array;
        return $this;
    }

    public function getModes()
    {
        return $this->_modes;
    }

    public function isMode($mode)
    {
        $mode=explode('-',$mode);
        return isset($this->getModes()[$mode[0]][$mode[1]]);
    }

    public function setEnabled($bool)
    {
        $this->_enabled=(bool) $bool;
        return $this;
    }

    public function isEnabled()
    {
        return $this->_enabled;
    }

    public function save()
    {
        try {
            $model = $this->getModelClass();
            $model::instance()->save($this);
        }  catch (ModelException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    public function formatFrom($from, $data)
    {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                ->setKey($data['Key'])
                ->setTitle(@unserialize($data['Title']))
                ->setDescription(@unserialize($data['Description']))
                ->setModes(@unserialize($data['Modes']))
                ->setOptions(@unserialize($data['Options']))
                ->setEnabled($data['Enabled']);
        }

        return $this;
    }

}

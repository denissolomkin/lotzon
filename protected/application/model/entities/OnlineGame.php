<?php

class OnlineGame extends Entity
{
    private $_id = 0;
    private $_key = '';
    private $_title = '';
    private $_description = '';
    private $_options = '';
    private $_modes = '';
    private $_audio = '';
    private $_enabled = true;
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
            if(isset($this->_title[$lang]) && $this->_title[$lang] && $this->_title[$lang]!='')
                $title = $this->_title[$lang];
            else
                $title = reset($this->_title);
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
            if(isset($this->_description[$lang]) && $this->_description[$lang] && $this->_description[$lang]!='')
                $description = nl2br($this->_description[$lang]);
            else
                $description = nl2br(reset($this->_description));;
        } else
            $description = $this->_description;

        return $description;
    }

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

    public function setAudio($array)
    {
        $this->_audio=$array;
        return $this;
    }

    public function getAudio()
    {
        return $this->_audio;
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
                ->setAudio(@unserialize($data['Audio']))
                ->setEnabled($data['Enabled']);
        }

        return $this;
    }

}

<?php 

class GameSettings extends Entity
{
    private $_key = '';
    private $_title = array();
    private $_options = array();
    private $_games = array();


    public function init()
    {
        $this->setModelClass('GameSettingsModel');
    }

    public function setKey($string)
    {
        $this->_key = $string;

        return $this;
    }

    public function getKey()
    {
        return $this->_key;
    }

    public function setTitle($string)
    {
        $this->_title = $string;

        return $this;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setOptions($array)
    {
        $this->_options = $array;

        return $this;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function getOption($key)
    {
        return isset($this->_options[$key])?$this->_options[$key]:null;
    }

    public function setGames($array)
    {
        $this->_games = $array;

        return $this;
    }

    public function getGames()
    {
        return $this->_games;
    }

    public function getRandomGame()
    {
        return is_array($this->_games) && !empty($this->_games) ? array_rand($this->_games) : null;

    }

    public function validate()
    {
        return true;
    }

    public function formatFrom($from, $data)
    {
        if ($from == 'DB') {
            $this->setKey($data['Key'])
                ->setTitle($data['Title'])
                ->setOptions(@unserialize($data['Options']))
                ->setGames(@unserialize($data['Games']));
        }

        return $this;
    }
}

<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/processors/PlayersDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/PlayersCacheProcessor.php');


class PlayersModel extends Model
{
    public function init()
    {
        $this->setProcessor(Config::instance()->cacheEnabled ? new PlayersCacheProcessor() : new PlayersDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getPlayersCount()
    {
        return $this->getProcessor()->getPlayersCount();
    }

    public function getList($limit = 0, $offset = 0)
    {
        return $this->getProcessor()->getList($limit, $offset);
    }

    public function checkNickname(Entity $player) 
    {
        return $this->getProcessor()->checkNickname($player);   
    }

    public function saveAvatar(Entity $player) 
    {
        return $this->getProcessor()->saveAvatar($player);   
    }

    public function changePassword(Entity $player) 
    {
        return $this->getProcessor()->changePassword($player);      
    }
}
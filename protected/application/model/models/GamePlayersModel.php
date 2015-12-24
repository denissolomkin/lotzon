<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/GamePlayer.php');
Application::import(PATH_APPLICATION . 'model/processors/GamePlayersDBProcessor.php');

class GamePlayersModel extends Model
{
    public function init()
    {
        parent::init();
        $this->setProcessor(new GamePlayersDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList()
    {
        return $this->getProcessor()->getList();
    }

    public function getOnline($gameId)
    {
        return $this->getProcessor()->getOnline($gameId);
    }

    public function getStack($key = null, $mode = null)
    {
        return $this->getProcessor()->getStack($key, $mode);
    }

}

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

    public function getList($ping)
    {
        return $this->getProcessor()->getList($ping);
    }

    public function getAvailableBots()
    {
        return $this->getProcessor()->getAvailableBots();
    }

    public function updateBotsPing()
    {
        return $this->getProcessor()->updateBotsPing();
    }

    public function getOnline($gameId)
    {
        return $this->getProcessor()->getOnline($gameId);
    }

    public function hasStack($key = null, $mode = null)
    {
        return $this->getProcessor()->hasStack($key, $mode);
    }

    public function getStack($key = null, $mode = null)
    {
        return $this->getProcessor()->getStack($key, $mode);
    }

}

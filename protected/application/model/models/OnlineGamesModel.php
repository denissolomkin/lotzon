<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/OnlineGame.php');
Application::import(PATH_APPLICATION . 'model/processors/OnlineGamesDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/OnlineGamesCacheProcessor.php');


class OnlineGamesModel extends Model
{
    public function init()
    {
        $this->setProcessor(Config::instance()->cacheEnabled ? new OnlineGamesCacheProcessor() : new OnlineGamesDBProcessor());
    }

    public function getList()
    {
        return $this->getProcessor()->getList();
    }

    public function getGame($key)
    {
        return $this->getProcessor()->getGame($key);
    }

    public function save(Entity $game)
    {
        return $this->getProcessor()->save($game);
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

}
<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/GameConstructor.php');
Application::import(PATH_APPLICATION . 'model/processors/GameConstructorDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/GameConstructorCacheProcessor.php');


class GameConstructorModel extends Model
{
    public function init()
    {
        $this->setProcessor(Config::instance()->cacheEnabled ? new GameConstructorCacheProcessor() : new GameConstructorDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList()
    {
        return $this->getProcessor()->getList();
    }

    public function getGame($key)
    {
        return $this->getProcessor()->getList()['online'][$key];
    }

    public function recache()
    {
        return $this->getProcessor()->recache();
    }
}
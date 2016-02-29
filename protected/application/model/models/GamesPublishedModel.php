<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/GamePublished.php');
Application::import(PATH_APPLICATION . 'model/processors/GamesPublishedDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/GamesPublishedCacheProcessor.php');

class GamesPublishedModel extends Model
{
    public function init()
    {
        $this->setProcessor(Config::instance()->cacheEnabled ? new GamesPublishedCacheProcessor() : new GamesPublishedDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList()
    {
        return $this->getProcessor()->getList();
    }

    public function recache()
    {
        return $this->getProcessor()->recache();
    }

}
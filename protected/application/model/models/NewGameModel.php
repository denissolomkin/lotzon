<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/NewGame.php');
Application::import(PATH_APPLICATION . 'model/processors/NewGameDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/NewGameCacheProcessor.php');


class NewGameModel extends Model
{
    public function init()
    {
        $this->setProcessor(Config::instance()->cacheEnabled ? new NewGameCacheProcessor() : new NewGameDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

}
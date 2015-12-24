<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/GameApp.php');
Application::import(PATH_APPLICATION . 'model/processors/GameAppsDBProcessor.php');

class GameAppsModel extends Model
{
    public function init()
    {
        parent::init();
        $this->setProcessor(new GameAppsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList($key = null)
    {
        return $this->getProcessor()->getList($key);
    }

}

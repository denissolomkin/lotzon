<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/GameSettings.php');
Application::import(PATH_APPLICATION . 'model/processors/GameSettingsDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/GameSettingsCacheProcessor.php');

class GameSettingsModel extends Model
{
    public function init()
    {
        $this->setProcessor(Config::instance()->cacheEnabled ? new GameSettingsCacheProcessor() : new GameSettingsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function save(Entity $chanceGame)
    {
        return $this->getProcessor()->save($chanceGame);
    }

    public function getSettings($key)
    {
        return $this->getProcessor()->getList()[$key];
    }

    public function getList()
    {
        return $this->getProcessor()->getList();
    }

}
<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/processors/GameSettingsCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/GameSettingsDBProcessor.php');

class GameSettingsModel extends Model
{
    public function init()
    {
        parent::init();

        $this->setProcessor(Config::instance()->cacheEnabled ? new GameSettingsCacheProcessor() : new GameSettingsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function saveSettings(GameSettings $settings)
    {
        return $this->getProcessor()->saveSettings($settings);
    }

    public function loadSettings()
    {
        return $this->getProcessor()->loadSettings();   
    }

    public function create(Entity $object) 
    {
        throw new ModelException("Direct settings manupilation disabled", 500);
        
    }

    public function fetch(Entity $object)
    {
        throw new ModelException("Direct settings manupilation disabled", 500);
    }   

    public function update(Entity $object)
    {
        throw new ModelException("Direct settings manupilation disabled", 500);
    }

    public function delete(Entity $object)
    {
        throw new ModelException("Direct settings manupilation disabled", 500);
    }
}
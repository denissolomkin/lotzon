<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/processors/LotterySettingsCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/LotterySettingsDBProcessor.php');

class LotterySettingsModel extends Model
{
    public function init()
    {
        parent::init();

        $this->setProcessor(Config::instance()->cacheEnabled ? new LotterySettingsCacheProcessor() : new LotterySettingsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function saveSettings(LotterySettings $settings)
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
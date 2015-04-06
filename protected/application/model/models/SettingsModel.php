<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/Settings.php');
Application::import(PATH_APPLICATION . 'model/processors/SettingsDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/SettingsCacheProcessor.php');

class SettingsModel extends Model
{
    private $_list;
    public function init()
    {
        parent::init();

        $this->setProcessor(Config::instance()->cacheEnabled ? new SettingsCacheProcessor() : new SettingsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList()
    {
        if(!$this->_list) // one query - one session
            $this->_list = $this->getProcessor()->getList();
        return $this->_list;
    }

    public function getSettings($key)
    {
        if(isset($this->getList()[$key])){
            $settings=$this->getList()[$key];
        } else {
            $settings = new Settings;
            $settings->setKey($key);
        }

        return $settings;
    }

    public function save($key, $value)
    {
    }

    public function fetch(Entity $settings) {

        try {
            $settings = $this->getProcessor()->fetch($settings);
        } catch (ModelException $e) {
            throw new EntityException("Model Error", 500);
        }

        return $settings;
    }

}

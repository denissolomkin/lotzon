<?php

Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/GameSettingsDBProcessor.php');

class GameSettingsCacheProcessor extends BaseCacheProcessor
{

    const SETTINGS_CACHE_KEY = "game::settings";

    public function init()
    {
        $this->setBackendProcessor(new GameSettingsDBProcessor());
    }

    public function saveSettings(GameSettings $settings)
    {
        $settings = $this->getBackendProcessor()->saveSettings($settings);

        if (!Cache::init()->set(self::SETTINGS_CACHE_KEY, $settings)) {
            throw new ModelException("Unable to cache storage data", 500);            
        }

        return $settings;
    }

    public function loadSettings()
    {
        if (($settings = Cache::init()->get(self::SETTINGS_CACHE_KEY)) !== false) {
            return $settings;
        }

        $settings = $this->getBackendProcessor()->loadSettings();
        Cache::init()->set(self::SETTINGS_CACHE_KEY, $settings);

        return $settings;
    }
}
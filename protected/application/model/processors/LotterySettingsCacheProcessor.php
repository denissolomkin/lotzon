<?php

Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/LotterySettingsDBProcessor.php');

class LotterySettingsCacheProcessor extends BaseCacheProcessor
{

    const LOTTERY_SETTINGS_KEY = "lottery::settings";

    public function init()
    {
        $this->setBackendProcessor(new LotterySettingsDBProcessor());
    }

    public function saveSettings(LotterySettings $settings)
    {
        $settings = $this->getBackendProcessor()->saveSettings($settings);

        if (!Cache::init()->set(self::LOTTERY_SETTINGS_KEY, $this->getBackendProcessor()->loadSettings())) {
            throw new ModelException("Unable to cache storage data", 500);            
        }

        return $settings;
    }

    public function loadSettings()
    {
        if (($settings = Cache::init()->get(self::LOTTERY_SETTINGS_KEY)) !== false) {
            return $settings;
        }

        $settings = $this->getBackendProcessor()->loadSettings();
        Cache::init()->set(self::LOTTERY_SETTINGS_KEY, $settings);

        return $settings;
    }
}
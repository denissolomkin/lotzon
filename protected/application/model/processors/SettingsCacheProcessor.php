<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/SettingsDBProcessor.php');

class SettingsCacheProcessor extends BaseCacheProcessor implements IProcessor
{

    const LIST_CACHE_KEY = "settings::list";

    public function init()
    {
        $this->setBackendProcessor(new SettingsDBProcessor());
    }

    public function create(Entity $settings)
    {
        $settings = $this->getBackendProcessor()->create($settings);
        $this->getList(true);
        return $settings;
    }

    public function getList($recache=false)
    {
        if (($list = Cache::init()->get(self::LIST_CACHE_KEY)) === false OR $recache) {
            $list = $this->getBackendProcessor()->getList();

            if (!Cache::init()->set(self::LIST_CACHE_KEY , $list)) {
                throw new ModelException("Unable to cache storage data", 500);
            }
        }
        return $list;
    }


    public function update(Entity $settings) {
    }

    public function fetch(Entity $settings) {

        $list = $this->getList();
        if(isset($list[$settings->getKey]) && $settings->formatFrom('CLASS',$list[$settings->getKey])) {
            return $settings;
        } else
            throw new ModelException("Settings not found", 404);
    }

    public function delete(Entity $currency) {
    }
}
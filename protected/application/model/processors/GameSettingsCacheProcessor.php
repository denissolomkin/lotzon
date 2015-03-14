<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/GameSettingsDBProcessor.php');

class GameSettingsCacheProcessor extends BaseCacheProcessor implements IProcessor
{

    const LIST_CACHE_KEY = "games::settings";

    public function init()
    {
        $this->setBackendProcessor(new GameSettingsDBProcessor());
    }

    public function create(Entity $game)
    {
        $game = $this->getBackendProcessor()->create($game);
        $this->getList(true);
        return $game;
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

    public function getGame($key)
    {
        if (($list = Cache::init()->get(self::LIST_CACHE_KEY)) === false) {
            $list = $this->getBackendProcessor()->getList();
            if (!Cache::init()->set(self::LIST_CACHE_KEY , $list)) {
                throw new ModelException("Unable to cache storage data", 500);
            }
        }
        return $list[$key];
    }

    public function update(Entity $game) {
        $game = $this->getBackendProcessor()->update($game);
        $this->getList(true);
        return $game;
    }


    public function delete(Entity $player)
    {
    }

    public function fetch(Entity $player)
    {
    }

}
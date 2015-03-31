<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/QuickGamesDBProcessor.php');

class QuickGamesCacheProcessor extends BaseCacheProcessor implements IProcessor
{

    const PLAYER_CACHE_KEY = "players::%s";
    const PLAYERS_COUNT_CACHE_KEY = "players::count";
    const LIST_CACHE_KEY = "games::quick";

    public function init()
    {
        $this->setBackendProcessor(new QuickGamesDBProcessor());
    }

    public function create(Entity $game) {
        $game = $this->getBackendProcessor()->create($game);
        return $game;
    }

    public function getGamesSettings()
    {
        return $this->getBackendProcessor()->getGamesSettings();
    }

    public function getRandomGame()
    {
        return $this->getBackendProcessor()->getRandomGame();
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

    public function getGame($key) {
        if (($list = Cache::init()->get(self::LIST_CACHE_KEY)) === false)
        {
            $list = $this->getBackendProcessor()->getList();
            if (!Cache::init()->set(self::LIST_CACHE_KEY , $list)) {
                throw new ModelException("Unable to cache storage data", 500);
            }
        }
        return $list[$key];
    }

    public function save(Entity $game)
    {
        $game = $this->getBackendProcessor()->save($game);
        $this->getList(true);
        return $game;
    }

    public function update(Entity $player)
    {
        return true;
    }


    public function fetch(Entity $player)
    {
        return true;
    }

    public function delete(Entity $player)
    {
        return true;
    }

}
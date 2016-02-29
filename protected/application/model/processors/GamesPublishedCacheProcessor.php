<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/GamesPublishedDBProcessor.php');

class GamesPublishedCacheProcessor extends BaseCacheProcessor implements IProcessor
{
    const LIST_CACHE_KEY = "games::published";

    public function init()
    {
        $this->setBackendProcessor(new GamesPublishedDBProcessor());
    }

    public function getList()
    {
        if (($list = Cache::init()->get(self::LIST_CACHE_KEY)) === false) {
            $list = $this->recache();
        }

        return $list;
    }

    public function update(Entity $game)
    {
        $game = $this->getBackendProcessor()->update($game);
        $this->recache();

        return $game;
    }

    public function recache()
    {
        /* todo delete after merge LOT-22 */
        if(\Config::instance()->cacheEnabled){
            Cache::init()->delete("games::settings");
        }

        $list = $this->getBackendProcessor()->getList();
        if (!Cache::init()->set(self::LIST_CACHE_KEY, $list)) {
            throw new ModelException("Unable to cache storage data", 500);
        }

        return $list;
    }

    public function fetch(Entity $game)
    {
    }

    public function create(Entity $game)
    {
    }

    public function delete(Entity $game)
    {
    }

}
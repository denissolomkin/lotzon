<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/OnlineGamesDBProcessor.php');

class OnlineGamesCacheProcessor extends BaseCacheProcessor implements IProcessor
{

    const LIST_CACHE_KEY = "games::online";
    const RATING_CACHE_KEY = "games::rating";

    public function init()
    {
        $this->setBackendProcessor(new OnlineGamesDBProcessor());
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

    public function save(Entity $game) {
        $game = $this->getBackendProcessor()->save($game);
        $this->getList(true);
        return $game;
    }

    public function recacheRating()
    {
        $rating = $this->getBackendProcessor()->getRating();
        if (!Cache::init()->set(self::RATING_CACHE_KEY , $rating)) {
            throw new ModelException("Unable to cache storage data", 500);
        }

        return true;
    }

    public function getRating($gameId)
    {
        if (($rating = Cache::init()->get(self::RATING_CACHE_KEY)) === false) {
            $rating = $this->getBackendProcessor()->getRating();
            if (!Cache::init()->set(self::LIST_CACHE_KEY , $rating)) {
                throw new ModelException("Unable to cache storage data", 500);
            }
        }

        return isset($rating[$gameId]) ? $rating[$gameId] : null;
    }


    public function update(Entity $player) {
    }


    public function fetch(Entity $player) {
    }

    public function delete(Entity $player) {
    }
}
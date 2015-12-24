<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/GameConstructorDBProcessor.php');

class GameConstructorCacheProcessor extends BaseCacheProcessor implements IProcessor
{

    const LIST_CACHE_KEY   = "games::list";

    public function init()
    {
        $this->setBackendProcessor(new GameConstructorDBProcessor());
    }

    public function getList()
    {
        if (($list = Cache::init()->get(self::LIST_CACHE_KEY)) === false) {
            $list = $this->recache();
        }

        return $list;
    }

    public function create(Entity $game)
    {
        $game = $this->getBackendProcessor()->create($game);
        $this->recache();

        return $game;
    }

    public function update(Entity $game)
    {
        $game = $this->getBackendProcessor()->update($game);
        $this->recache();

        return $game;
    }

    public function fetch(Entity $game)
    {
        $list = $this->getList();
        if(isset($list[$game->getType()])
            && isset($list[$game->getType()][$game->getId()?:$game->getKey()])
            && $fetch = $list[$game->getType()][$game->getId()?:$game->getKey()])
            $game->formatFrom('DB', $fetch->export('DB'));
        return $game;
    }

    function recache()
    {

        /* todo delete after merge LOT-22 */
        if(\Config::instance()->cacheEnabled){
            Cache::init()->delete("games::online");
            Cache::init()->delete("games::quick");
            Cache::init()->delete("games::settings");
            Cache::init()->delete("games::published");
        }

        $list = $this->getBackendProcessor()->getList();

        if (!Cache::init()->set(self::LIST_CACHE_KEY, $list)) {
            throw new ModelException("Unable to cache storage data", 500);
        }

        return $list;
    }

    public function delete(Entity $player)
    {
        return true;
    }

}
<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/PlayersDBProcessor.php');

class PlayersCacheProcessor extends BaseCacheProcessor implements IProcessor
{

    const PLAYER_CACHE_KEY = "players::%s";
    const PLAYERS_COUNT_CACHE_KEY = "players::count";

    public function init()
    {
        $this->setBackendProcessor(new PlayersDBProcessor());
    }

    public function create(Entity $player)
    {
        $player = $this->getBackendProcessor()->create($player);
        $this->incrementPlayersCountCache();
        return $player;
    }

    protected function cachePlayer(Player $player)
    {
        if (!Cache::init()->set($this->playerCacheKey($player), $player)) {
            throw new ModelException("Unable to cache storage data", 500);
        }

        return $player;
    }

    public function update(Entity $player) {
        $player = $this->getBackendProcessor()->update($player);

        $this->cachePlayer($player);

        return $player;
    }

    public function fetch(Entity $player)
    {
        $cache = Cache::init()->get($this->playerCacheKey($player));
        if (!$cache) {
            $player = $this->getBackendProcessor()->fetch($player);
        } else {
            $player = $cache;
        }

        return $player;
    }

    public function delete(Entity $player) 
    {
        if ($this->getBackendProcessor()->delete($player)) {
            Cache::init()->delete($player);
            $this->decrementPlayersCacheCount();
        }
        $this->decrementPlayersCacheCount();
        return true;
    }

    protected function playerCacheKey(Player $player) {
        return sprintf(self::PLAYER_CACHE_KEY, $player->getEmail());
    }

    public function incrementPlayersCountCache()
    {
        if (!($count = Cache::init()->increment(self::PLAYERS_COUNT_CACHE_KEY))) {
            $count = $this->getBackendProcessor()->getAllPlayersCount();
            Cache::init()->set(self::PLAYERS_COUNT_CACHE_KEY, $count);
        }

        return $count;
    }

    public function decrementPlayersCacheCount()
    {
        if (!($count = Cache::init()->decrement(self::PLAYERS_COUNT_CACHE_KEY))) {
            $count = $this->getBackendProcessor()->getAllPlayersCount();
            Cache::init()->set(self::PLAYERS_COUNT_CACHE_KEY, $count);
        }

        return $count;
    }

    public function getPlayersCount()
    {
        if (!($count = Cache::init()->get(self::PLAYERS_COUNT_CACHE_KEY))) {
            $count = $this->getBackendProcessor()->getPlayersCount();
            Cache::init()->set(self::PLAYERS_COUNT_CACHE_KEY, $count);
        }        

        return $count;
    }
}
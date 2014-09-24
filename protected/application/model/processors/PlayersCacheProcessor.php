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
        $this->setBackendProcessor(new ShopDBProcessor());
    }

    public function create(Entity $player)
    {
        $player = $this->getBackendProcessor()->create($player);

        $this->cachePlayer($player);

        return $player;
    }

    protected function cachePlayer(Player $player)
    {
        if (!Cache::init()->set($key, $this->playerCacheKey($player))) {
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
        if (!($player = Cache::init()->get($this->playerCacheKey($player)))) {
            $player = $this->getBackendProcessor()->fetch($player);
        }

        return $player;
    }

    public function delete(Entity $player) 
    {
        if ($this->getBackendProcessor()->delete($player)) {
            !Cache::init()->delete($player);
        }

        return true;
    }

    protected function playerCacheKey(Player $player) {
        return sprintf(self::PLAYER_CACHE_KEY, $player->getEmail());
    }
}
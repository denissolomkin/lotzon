<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/OnlineGamesDBProcessor.php');

class OnlineGamesCacheProcessor extends BaseCacheProcessor implements IProcessor
{

    const PLAYER_CACHE_KEY = "players::%s";
    const PLAYERS_COUNT_CACHE_KEY = "players::count";
    const LIST_CACHE_KEY = "games::online";

    public function init()
    {
        $this->setBackendProcessor(new OnlineGamesDBProcessor());
    }

    public function create(Entity $game)
    {
        $game = $this->getBackendProcessor()->create($game);
        $this->incrementPlayersCountCache();
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

    public function getList2($lang, $limit = null, $offset = null)
    {


        if ($limit + $offset > Config::instance()->newsCacheCount) {
            return $this->getBackendProcessor()->getList($lang, $limit, $offset);
        }
        if (($list = Cache::init()->get(sprintf(self::LIST_CACHE_KEY, $lang))) !== false) {
            if (!is_null($limit) || !is_null($offset)) {
                $list = array_slice($list, $offset, $limit);
            }
        } else {
            $list = $this->getBackendProcessor()->getList($lang, $limit, $offset);
            $this->cacheLatest();
        }

        return $list;
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

        $this->cachePlayer($player->fetch());

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


    public function getPlayersCount()
    {
        if (!($count = Cache::init()->get(self::PLAYERS_COUNT_CACHE_KEY))) {
            $count = $this->getBackendProcessor()->getPlayersCount();
            Cache::init()->set(self::PLAYERS_COUNT_CACHE_KEY, $count);
        }        

        return $count;
    }
}
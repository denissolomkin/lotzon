<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/OnlineGamesDBProcessor.php');

class OnlineGamesCacheProcessor extends BaseCacheProcessor implements IProcessor
{

    const LIST_CACHE_KEY = "games::online";
    const RATING_CACHE_KEY = "games::rating";
    const FUND_CACHE_KEY = "games::fund";

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

    public function getRating($gameId = null, $playerId = null)
    {
        if (($rating = Cache::init()->get(self::RATING_CACHE_KEY)) === false) {
            $rating = $this->getBackendProcessor()->getRating();
            if (!Cache::init()->set(self::RATING_CACHE_KEY , $rating)) {
                throw new ModelException("Unable to cache storage data", 500);
            }
        }

        /* Replace rating of all players by rating of concrete player */
        if(isset($playerId))
            foreach ($rating[$gameId] as $currency => &$players)
                $players = isset($players["#".$playerId]) ? $players["#".$playerId] : null;

        /* Replace rating of all games by rating of concrete game or player */
        if(isset($gameId))
            $rating = $rating[$gameId];

        return $rating;

    }

    public function getPlayerRating($gameId = null, $playerId = null)
    {
        return $this->getBackendProcessor()->getPlayerRating($gameId, $playerId);

    }

    public function getFund($gameId = null)
    {
        if (($fund = Cache::init()->get(self::FUND_CACHE_KEY)) === false) {
            $fund = $this->getBackendProcessor()->getFund();
            if (!Cache::init()->set(self::FUND_CACHE_KEY , $fund)) {
                throw new ModelException("Unable to cache storage data", 500);
            }
        }

        return isset($fund[$gameId]) ? $fund[$gameId] : array();
    }

    public function recacheRatingAndFund()
    {
        $rating = $this->getBackendProcessor()->getRating();
        if (!Cache::init()->set(self::RATING_CACHE_KEY , $rating)) {
            throw new ModelException("Unable to cache storage data", 500);
        }

        $fund = $this->getBackendProcessor()->getFund();
        if (!Cache::init()->set(self::FUND_CACHE_KEY , $fund)) {
            throw new ModelException("Unable to cache storage data", 500);
        }

        return true;
    }

    public function update(Entity $player) {
    }


    public function fetch(Entity $player) {
    }

    public function delete(Entity $player) {
    }
}
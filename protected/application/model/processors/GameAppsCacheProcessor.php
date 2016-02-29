<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/GameAppsDBProcessor.php');

class GameAppsCacheProcessor extends BaseCacheProcessor implements IProcessor
{
    const RATING_CACHE_KEY = "games::rating";
    const FUND_CACHE_KEY = "games::fund";

    public function init()
    {
        $this->setBackendProcessor(new GameAppsDBProcessor());
    }

    public function getRating($gameId = null)
    {
        if (($rating = Cache::init()->get(self::RATING_CACHE_KEY)) === false) {
            $rating = $this->getBackendProcessor()->getRating();
            if (!Cache::init()->set(self::RATING_CACHE_KEY , $rating)) {
                throw new ModelException("Unable to cache storage data", 500);
            }
        }

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

        return $fund;
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

}
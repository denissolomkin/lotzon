<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/OnlineGame.php');
Application::import(PATH_APPLICATION . 'model/processors/OnlineGamesDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/OnlineGamesCacheProcessor.php');


class OnlineGamesModel extends Model
{
    public function init()
    {
        $this->setProcessor(Config::instance()->cacheEnabled ? new OnlineGamesCacheProcessor() : new OnlineGamesDBProcessor());
    }

    public function getList()
    {
        return $this->getProcessor()->getList();
    }

    public function getGame($key)
    {
        return $this->getProcessor()->getGame($key);
    }

    public function save(Entity $game)
    {
        return $this->getProcessor()->save($game);
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getRating($gameId = null)
    {
        $rating = $this->getProcessor()->getRating($gameId);

        if($gameId) {
            $rating = isset($rating[$gameId]) ? $rating[$gameId] : array();
        }

        return $rating;
    }

    public function getFund($gameId = null)
    {
        $fund = $this->getProcessor()->getFund($gameId);

        if($gameId) {
            $fund = isset($fund[$gameId]) ? $fund[$gameId] : array();
        }

        return $fund;
    }

    public function getPlayerRating($gameId = null, $playerId = null)
    {
        return $this->getProcessor()->getPlayerRating($gameId, $playerId);
    }


    public function getGameTop($month = null)
    {
        return $this->getProcessor()->getGameTop($month);
    }

    public function saveGameTop($data = array())
    {
        return $this->getProcessor()->saveGameTop($data);
    }

    public function deleteGameTop($id = null)
    {
        return $this->getProcessor()->deleteGameTop($id);
    }

    public function incrementGameTop()
    {
        return $this->getProcessor()->incrementGameTop();
    }

    public function recacheRatingAndFund()
    {
        return $this->getProcessor()->recacheRatingAndFund();
    }

}
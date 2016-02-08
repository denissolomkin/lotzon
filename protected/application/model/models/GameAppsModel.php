<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/GameApp.php');
Application::import(PATH_APPLICATION . 'model/processors/GameAppsCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/GameAppsDBProcessor.php');

class GameAppsModel extends Model
{
    public function init()
    {
        parent::init();
        $this->setProcessor(Config::instance()->cacheEnabled ? new GameAppsCacheProcessor() : new GameAppsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    /* GamesTmpApps Table */

    public function getApp($uid = null)
    {
        $gameApp = new \GameApp;
        return ($gameApp->setUid($uid)->fetch() ? $gameApp->getApp() : false);
    }

    public function countApps($key = null, $status = null)
    {
        return $this->getProcessor()->countApps($key, $status);
    }

    public function countWaitingApps($key = null)
    {
        return $this->getProcessor()->countApps($key, 0);
    }

    public function countRunningApps($key = null)
    {
        return $this->getProcessor()->countApps($key, 1);
    }

    public function getList($key = null)
    {
        return $this->getProcessor()->getList($key);
    }

    /* PlayerGames Table */

    public static function saveResults(Game $app)
    {
        return self::instance()->getProcessor()->saveResults($app);
    }

    public function getRating($gameId = null, $limit = null, $offset = 0)
    {
        $rating = $this->getProcessor()->getRating($gameId);

        if ($gameId) {
            $rating = isset($rating[$gameId]) ? $rating[$gameId] : array();
        }

        return $rating;
    }

    public function getPlayerRating($gameId = null, $playerId = null)
    {
        return $this->getProcessor()->getPlayerRating($gameId, $playerId);
    }

    public function getFund($gameId = null)
    {
        $fund = $this->getProcessor()->getFund($gameId);

        if ($gameId) {
            $fund = isset($fund[$gameId]) ? $fund[$gameId] : array();
        }

        return $fund;
    }

    public function recacheRatingAndFund()
    {
        return $this->getProcessor()->recacheRatingAndFund();
    }

    /* OnlineGamesTop Table */

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

}

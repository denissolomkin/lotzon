<?php
use \CountriesModel, \Player, \Game, \OnlineGamesModel;

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/Game.php');
Application::import(PATH_APPLICATION . 'model/processors/GamesDBProcessor.php');

class GamesModel extends Model
{
    public function init()
    {
        parent::init();

        $this->setProcessor(new GamesDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    /* OnlineGames Table */

    public static function getList()
    {
        return self::instance()->getProcessor()->getList();
    }

    public static function getItem()
    {
        return self::instance()->getProcessor()->getItem();
    }

    /* PlayerGames Table */

    public static function saveResults(Game $app)
    {
        return self::instance()->getProcessor()->saveResults($app);
    }

    public static function getFund()
    {
        return OnlineGamesModel::instance()->getProcessor()->getFund();
    }

    public static function getRating()
    {
        return OnlineGamesModel::instance()->getProcessor()->getRating();
    }

}

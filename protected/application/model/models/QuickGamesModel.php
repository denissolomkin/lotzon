<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/QuickGame.php');
Application::import(PATH_APPLICATION . 'model/processors/QuickGamesDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/QuickGamesCacheProcessor.php');

class QuickGamesModel extends Model
{
    public function init()
    {
        //$this->setProcessor(Config::instance()->cacheEnabled ? new QuickGamesCacheProcessor() : new QuickGamesDBProcessor());
        $this->setProcessor(new QuickGamesCacheProcessor());

    }

    public static function myClassName()
    {
        return __CLASS__;
    }

   public function save(Entity $game)
   {
        return $this->getProcessor()->save($game);
   }

   public function getGamesSettings()
   {
      return $this->getProcessor()->getGamesSettings();
   }

    public function getList()
    {
        return $this->getProcessor()->getList();
    }

    public function getRandomGame()
    {
        return $this->getProcessor()->getRandomGame();
    }

   public function logWin($game, $combination, $clicks, $player, $item)
   {
    return $this->getProcessor()->logWin($game, $combination, $clicks, $player, $item);
   }
}
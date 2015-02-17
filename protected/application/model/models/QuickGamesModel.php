<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/QuickGame.php');
Application::import(PATH_APPLICATION . 'model/processors/QuickGamesProcessor.php');

class QuickGamesModel extends Model
{
    public function init()
    {
        $this->setProcessor(new QuickGamesProcessor());
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

    public function getRandomGame()
    {
        return $this->getProcessor()->getRandomGame();
    }

   public function logWin($game, $combination, $clicks, $player, $item)
   {
    return $this->getProcessor()->logWin($game, $combination, $clicks, $player, $item);
   }
}
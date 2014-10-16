<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/ChanceGame.php');
Application::import(PATH_APPLICATION . 'model/processors/ChanceGamesProcessor.php');

class ChanceGamesModel extends Model
{
    public function init()
    {
        $this->setProcessor(new ChanceGamesProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

   public function save(Entity $chanceGame) 
   {
        return $this->getProcessor()->save($chanceGame);
   }

   public function getGamesSettings()
   {
      return $this->getProcessor()->getGamesSettings();
   }

   public function logWin($game, $combination, $clicks, $player, $item)
   {
    return $this->getProcessor()->logWin($game, $combination, $clicks, $player, $item);
   }

    public function getUnorderedChanceWinData($itemId, $player) { 
      return $this->getProcessor()->getUnorderedChanceWinData($itemId, $player);
    }
}
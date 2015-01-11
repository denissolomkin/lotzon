<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/processors/PlayersDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/PlayersCacheProcessor.php');


class PlayersModel extends Model
{
    public function init()
    {
        $this->setProcessor(Config::instance()->cacheEnabled ? new PlayersCacheProcessor() : new PlayersDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getPlayersCount($search=null)
    {
        return $this->getProcessor()->getPlayersCount($search);
    }

    public function getList($limit = 0, $offset = 0, $sort = array(), $search=null)
    {
        return $this->getProcessor()->getList($limit, $offset, $sort, $search);
    }

    public function getLog($playerId)
    {
        return $this->getProcessor()->getLog($playerId);
    }

    public function getReviews($playerId)
    {
        return $this->getProcessor()->getReviews($playerId);
    }

    public function getTickets($playerId, $lotteryId=0)
    {
        return $this->getProcessor()->getTickets($playerId,$lotteryId);
    }

    public function checkNickname(Entity $player) 
    {
        return $this->getProcessor()->checkNickname($player);   
    }
/*
    public function checkAdBlockNotices(Entity $player)
    {
        return $this->getProcessor()->checkAdBlockNotices($player);
    }
*/
    public function updateCounters(Entity $player)
    {
        return $this->getProcessor()->updateCounters($player);
    }

    public function updateSocial(Entity $player)
    {
        return $this->getProcessor()->updateSocial($player);
    }

    public function disableSocial(Entity $player)
    {
        return $this->getProcessor()->disableSocial($player);
    }

    public function isSocialUsed(Entity $player)
    {
        return $this->getProcessor()->isSocialUsed($player);
    }

    public function getBalance(Entity $player)
    {
        return $this->getProcessor()->getBalance($player);
    }

    public function updateBalance(Entity $player, $currency, $quantity)
    {
        return $this->getProcessor()->updateBalance($player, $currency, $quantity);
    }

    public function writeLog(Entity $player, $action, $desc='')
    {
        return $this->getProcessor()->writeLog($player, $action, $desc);
    }

    public function reportTrouble(Entity $player, $trouble)
    {
        return $this->getProcessor()->reportTrouble($player, $trouble);
    }

    public function saveAvatar(Entity $player) 
    {
        return $this->getProcessor()->saveAvatar($player);   
    }

    public function changePassword(Entity $player) 
    {
        return $this->getProcessor()->changePassword($player);      
    }

    public function decrementInvitesCount(Entity $player) 
    {
        return $this->getProcessor()->decrementInvitesCount($player);      
    }

    public function decrementSocialPostsCount(Entity $player) 
    {
        return $this->getProcessor()->decrementSocialPostsCount($player);
    }

    public function updateLastNotice(Entity $player)
    {
        return $this->getProcessor()->updateLastNotice($player);
    }

    public function markOnline(Entity $player)
    {
        return $this->getProcessor()->markOnline($player);   
    }

    public function validateHash($hash)
    {
        return $this->getProcessor()->validateHash($hash);
    }
}
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

    public function getPlayersStats()
    {
        return $this->getProcessor()->getPlayersStats();
    }

    public function getAvailableIds()
    {
        return $this->getProcessor()->getAvailableIds();
    }

    public function updateLastChance(Entity $player)
    {
        return $this->getProcessor()->updateLastChance($player);
    }

    public function updateCookieId(Entity $player, $cookie)
    {
        return $this->getProcessor()->updateCookieId($player, $cookie);
    }

    public function getList($limit = 0, $offset = 0, $sort = array(), $search=null)
    {
        return $this->getProcessor()->getList($limit, $offset, $sort, $search);
    }

    public function getLog($playerId, $action=null)
    {
        return $this->getProcessor()->getLog($playerId, $action);
    }

    public function getLogins($playerId)
    {
        return $this->getProcessor()->getLogins($playerId);
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

    public function getBalance(Entity $player, $forUpdate = false)
    {
        return $this->getProcessor()->getBalance($player, $forUpdate);
    }

    public function updateBalance(Entity $player, $currency, $quantity)
    {
        return $this->getProcessor()->updateBalance($player, $currency, $quantity);
    }

    public function writeLog(Entity $player, $options)
    {
        return $this->getProcessor()->writeLog($player, $options);
    }

    public function writeLogin(Entity $player)
    {
        return $this->getProcessor()->writeLogin($player);
    }


    public function reportTrouble(Entity $player, $trouble)
    {
        return $this->getProcessor()->reportTrouble($player, $trouble);
    }

    public function ban(Entity $player, $status=0)
    {
        return $this->getProcessor()->ban($player,$status);
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

    public function updateIp(Entity $player, $ip)
    {
        return $this->getProcessor()->updateIp($player, $ip);
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
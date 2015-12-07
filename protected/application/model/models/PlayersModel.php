<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/processors/PlayersDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/PlayersCacheProcessor.php');


class PlayersModel extends Model
{
    public function init()
    {
        //$this->setProcessor(Config::instance()->cacheEnabled ? new PlayersCacheProcessor() : new PlayersDBProcessor());
        $this->setProcessor(new PlayersDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getPlayersCount($search=null)
    {
        return $this->getProcessor()->getPlayersCount($search);
    }

    public function getMaxId()
    {
        return $this->getProcessor()->getMaxId();
    }

    public function getPlayersStats()
    {
        return $this->getProcessor()->getPlayersStats();
    }

    public function getAvailableIds()
    {
        return $this->getProcessor()->getAvailableIds();
    }

    public function checkDate($key, Entity $player)
    {
        return $this->getProcessor()->checkDate($key, $player);
    }

    public function checkLastGame($key, Entity $player)
    {
        return $this->getProcessor()->checkLastGame($key, $player);
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

    public function updateNewsSubscribe(Entity $player, $newsSubscribe)
    {
        return $this->getProcessor()->updateNewsSubscribe($player, $newsSubscribe);
    }

    public function getMults($playerId)
    {
        return $this->getProcessor()->getMults($playerId);
    }

    public function getReviews($playerId)
    {
        return $this->getProcessor()->getReviews($playerId);
    }

    public function getTickets($playerId)
    {
        return $this->getProcessor()->getTickets($playerId);
    }

    public function checkNickname(Entity $player)
    {
        return $this->getProcessor()->checkNickname($player);
    }

    public function checkQiwi(Entity $player)
    {
        return $this->getProcessor()->checkQiwi($player);
    }

    public function checkPhone(Entity $player)
    {
        return $this->getProcessor()->checkPhone($player);
    }

    public function checkYandexMoney(Entity $player)
    {
        return $this->getProcessor()->checkYandexMoney($player);
    }

    public function checkWebMoney(Entity $player)
    {
        return $this->getProcessor()->checkWebMoney($player);
    }

    public function initCounters(Entity $player)
    {
        return $this->getProcessor()->initCounters($player);
    }

    public function initDates(Entity $player)
    {
        return $this->getProcessor()->initDates($player);
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

    public function getInvitesCount(Entity $player)
    {
        return $this->getProcessor()->getInvitesCount($player);
    }

    public function decrementSocialPostsCount(Entity $player)
    {
        return $this->getProcessor()->decrementSocialPostsCount($player);
    }

    public function isExists($id)
    {
        return $this->getProcessor()->isExists($id);
    }

    public function updateIp(Entity $player, $ip)
    {
        return $this->getProcessor()->updateIp($player, $ip);
    }

    public function updateNotice(Entity $player)
    {
        return $this->getProcessor()->updateNotice($player);
    }

    public function updateLogin(Entity $player)
    {
        return $this->getProcessor()->updateLogin($player);
    }

    public function markOnline(Entity $player)
    {
        return $this->getProcessor()->markOnline($player);
    }

    public function validateHash($hash)
    {
        return $this->getProcessor()->validateHash($hash);
    }

    public function search($search)
    {
        return $this->getProcessor()->search($search);
    }
}

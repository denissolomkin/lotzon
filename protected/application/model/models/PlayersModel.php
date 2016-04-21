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

    public function getReferralsCount($playerId, $onlyActive = false)
    {
        return $this->getProcessor()->getReferralsCount($playerId, $onlyActive);
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

    public function getMessages($playerId)
    {
        return $this->getProcessor()->getMessages($playerId);
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

    public function initStats(Entity $player)
    {
        return $this->getProcessor()->initStats($player);
    }

    public function initCounters(Entity $player)
    {
        return $this->getProcessor()->initCounters($player);
    }

    public function initDates(Entity $player)
    {
        return $this->getProcessor()->initDates($player);
    }

    public function loadPrivacy(Entity $player)
    {
        return $this->getProcessor()->loadPrivacy($player);
    }

    public function updatePrivacy(Entity $player)
    {
        return $this->getProcessor()->updatePrivacy($player);
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

    public function ban(Entity $player)
    {
        return $this->getProcessor()->ban($player);
    }

    public function recacheBots()
    {
        return $this->getProcessor()->recacheBots();
    }
    
    public function create(Entity $player)
    {
        $player = $this->getProcessor()->create($player);
        if($player->isBot()){
            $this->recacheBots();
        }
        return $player;
    }

    public function update(Entity $player)
    {
        $player = $this->getProcessor()->update($player);
        if($player->isBot()){
            $this->recacheBots();
        }
        return $player;
    }

    public function delete(Entity $player)
    {
        $this->getProcessor()->delete($player);
        if($player->isBot()){
            $this->recacheBots();
        }
        return true;
    }

    public function bot(Entity $player)
    {
        $player = $this->getProcessor()->bot($player);
        $this->recacheBots();
        return $player;
    }

    public function saveAvatar(Entity $player)
    {
        if($player->isBot()){
            $this->recacheBots();
        }

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

    public function validateHash($hash, $email)
    {
        return $this->getProcessor()->validateHash($hash, $email);
    }

    public function search($search)
    {
        return $this->getProcessor()->search($search);
    }

    public function getReferrals($playerId, $limit = 10, $offset = NULL)
    {
        return $this->getProcessor()->getReferrals($playerId, $limit, $offset);
    }

    public function updateGoldTicket(Entity $player, $quantity)
    {
        return $this->getProcessor()->updateGoldTicket($player, $quantity);
    }

    public function getPlayersPing($ids)
    {
        return $this->getProcessor()->getPlayersPing($ids);
    }

    public function savePreregistration(Entity $player) {
        return $this->getProcessor()->savePreregistration($player);
    }

    public function loadPreregistration(Entity $player) {
        return $this->getProcessor()->loadPreregistration($player);
    }
}

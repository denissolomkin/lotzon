<?php

Application::import(PATH_APPLICATION . 'model/processors/FriendsDBProcessor.php');

class FriendsModel extends Model
{
    public function init()
    {
        $this->setProcessor(new FriendsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList($playerId, $count = null, $offset = null, $status = 1, $search = null)
    {
        return $this->getProcessor()->getList($playerId, $count, $offset, $status, $search);
    }

    public function updateRequest($playerId, $toPlayerId, $status)
    {
        return $this->getProcessor()->updateRequest($playerId, $toPlayerId, $status);
    }

    public function deleteRequest($playerId, $toPlayerId)
    {
        return $this->getProcessor()->deleteRequest($playerId, $toPlayerId);
    }

    public function addRequest($playerId, $toPlayerId)
    {
        return $this->getProcessor()->addRequest($playerId, $toPlayerId);
    }

    public function remove($playerId, $toPlayerId)
    {
        return $this->getProcessor()->remove($playerId, $toPlayerId);
    }

    public function getStatusCount($playerId, $status, $onlyToPlayer = false)
    {
        return $this->getProcessor()->getStatusCount($playerId, $status, $onlyToPlayer);
    }

    public function getStatus($playerId, $toPlayerId)
    {
        return $this->getProcessor()->getStatus($playerId, $toPlayerId);
    }

    public function isFriend($playerId, $toPlayerId)
    {
        return $this->getProcessor()->isFriend($playerId, $toPlayerId);
    }
}

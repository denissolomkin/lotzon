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

    public function getList($playerId, $count = null, $offset = null, $status = 1)
    {
        return $this->getProcessor()->getList($playerId, $count, $offset, $status);
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
}

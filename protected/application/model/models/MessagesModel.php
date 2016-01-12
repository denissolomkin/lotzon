<?php

Application::import(PATH_APPLICATION . 'model/entities/Message.php');
Application::import(PATH_APPLICATION . 'model/processors/MessagesDBProcessor.php');

class MessagesModel extends Model
{
    public function init()
    {
        $this->setProcessor(new MessagesDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getStatusCount($playerId, $status = 0)
    {
        return $this->getProcessor()->getStatusCount($playerId, $status);
    }

    public function getList($playerFirst, $playerSecond, $count = NULL, $beforeId = NULL, $afterId = NULL, $offset = NULL)
    {
        return $this->getProcessor()->getList($playerFirst, $playerSecond, $count, $beforeId, $afterId, $offset);
    }

    public function getUnreadMessages($playerId)
    {
        return $this->getProcessor()->getUnreadMessages($playerId);
    }

    public function getLastTalks($playerId, $count = NULL, $offset = NULL, $modifyDate = NULL)
    {
        return $this->getProcessor()->getLastTalks($playerId, $count, $offset, $modifyDate);
    }

    public function markRead($playerId, $toPlayerId)
    {
        return $this->getProcessor()->markRead($playerId, $toPlayerId);
    }

    public function setNotificationsDate($playerId, $time = NULL)
    {
        return $this->getProcessor()->setNotificationsDate($playerId, $time);
    }
}

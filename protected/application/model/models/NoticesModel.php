<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/Notice.php');
Application::import(PATH_APPLICATION . 'model/processors/NoticesDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/NoticesCacheProcessor.php');


class NoticesModel extends Model
{
    public function init()
    {
        $this->setProcessor(Config::instance()->cacheEnabled ? new NoticesCacheProcessor() : new NoticesDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList($playerId = null, $date = null, $limit = null, $offset = null) {
        return $this->getProcessor()->getList($playerId, $date, $limit, $offset);
    }

    public function getPlayerUnreadNotices(Player $player) {
        return $this->getProcessor()->getPlayerUnreadNotices($player);
    }
}
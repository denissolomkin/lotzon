<?php

Application::import(PATH_APPLICATION . 'model/processors/CommentsDBProcessor.php');

class CommentsModel extends Model
{
    public function init()
    {
        $this->setProcessor(new CommentsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getCount($module, $objectId, $status = 1)
    {
        return $this->getProcessor()->getCount($module, $objectId, $status);
    }

    public function getList($module, $objectId, $count, $beforeId = NULL, $afterId = NULL, $status = 1)
    {
        return $this->getProcessor()->getList($module, $objectId, $count, $beforeId, $afterId, $status);
    }

    public function getLikes($commentId)
    {
        return $this->getProcessor()->getLikes($commentId);
    }

    public function isLiked($commentId, $playerId)
    {
        return $this->getProcessor()->isLiked($commentId, $playerId);
    }

    public function like($commentId, $playerId)
    {
        return $this->getProcessor()->like($commentId, $playerId);
    }

    public function dislike($commentId, $playerId)
    {
        return $this->getProcessor()->dislike($commentId, $playerId);
    }
}

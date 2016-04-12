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

    public function getList($module, $objectId, $count = NULL, $beforeId = NULL, $afterId = NULL, $status = 1, $parentId = NULL, $modifyDate = NULL, $playerId = NULL)
    {
        $res = array();
        $comments = $this->getProcessor()->getList($module, $objectId, $count, $beforeId, $afterId, $status, $parentId, $modifyDate);

        if ($playerId) {
            $likes = $this->isLiked(array_keys($comments), $playerId);
        } else {
            $likes = array();
        }

        foreach ($comments as $id => $comment) {
            $res[$id] = $comment->export('JSON');
            if (!$comment->getParentId()) {
                $res[$id]['answers'] = $this->getList($module, $objectId, NULL, $beforeId = NULL, $afterId = NULL, $status, $id, NULL, $playerId);
            } else {
                $res[$id]['comment_id'] = $comment->getParentId();
            }
            if (isset($likes[$id])) {
                $res[$id]['is_liked'] = true;
            } else {
                $res[$id]['is_liked'] = false;
            }
        }

        return $res;
    }

    public function getLikes($commentId)
    {
        return $this->getProcessor()->getLikes($commentId);
    }

    public function canPlayerPublish($playerId)
    {
        return $this->getProcessor()->canPlayerPublish($playerId);
    }

    public function isLiked($commentsIds, $playerId)
    {
        return $this->getProcessor()->isLiked($commentsIds, $playerId);
    }

    public function like($commentId, $playerId)
    {
        return $this->getProcessor()->like($commentId, $playerId);
    }

    public function dislike($commentId, $playerId)
    {
        return $this->getProcessor()->dislike($commentId, $playerId);
    }

    public function setNotificationsDate($playerId, $time = NULL)
    {
        return $this->getProcessor()->setNotificationsDate($playerId, $time);
    }

    public function getNotificationsCount($playerId, $module = 'comments', $objectId = 0)
    {
        return $this->getProcessor()->getNotificationsCount($playerId, $module, $objectId);
    }

    public function getNotificationsList($playerId, $count = 10, $offset = NULL)
    {
        return $this->getProcessor()->getNotificationsList($playerId, $count, $offset);
    }
}

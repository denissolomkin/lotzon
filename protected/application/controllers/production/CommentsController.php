<?php
namespace controllers\production;
use \Application, \Player, \SettingsModel, \CommentsModel, \Comment;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class CommentsController extends \AjaxController
{
    private $session;

    static $notificationsPerPage;
    static $commentsPerPage;

    public function init()
    {
        self::$commentsPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('COMMENTS_PER_PAGE') ? : 10;
        self::$notificationsPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('NOTIFICATIONS_PER_PAGE') ? : 10;

        $this->session = new Session();
        parent::init();
    }

    private function authorizedOnly()
    {
        if (!$this->session->get(Player::IDENTITY) instanceof Player) {
            $this->ajaxResponseUnauthorized();
            return false;
        }
        $this->session->get(Player::IDENTITY)->markOnline();
        return true;
    }

    public function itemAction($commentId)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $comment = new Comment;
        $comment->setId($commentId)->fetch();

        $comments = array();
        $comments[$commentId] = $comment->export('JSON');

        if (!$comment->getParentId()) {
            $comments[$commentId]['answers'] = CommentsModel::instance()->getList($comment->getModule(), $comment->getObjectId(), NULL, NULL, NULL, 1, $commentId);
        } else {
            $comments[$commentId]['comment_id'] = $comment->getParentId();
        }

        $response = array(
            'res' => array(
                'communication' => array(
                    'comments' => $comments,
                ),
            ),
        );

        $this->ajaxResponseCode($response);
        return true;
    }

    public function listAction($module = 'comments', $objectId = 0)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $count    = $this->request()->get('count', self::$commentsPerPage);
        $beforeId = $this->request()->get('before_id', NULL);
        $afterId  = $this->request()->get('after_id', NULL);

        try {
            $list = CommentsModel::instance()->getList($module, $objectId, $count+1, $beforeId, $afterId);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array();

        if (count($list)<=$count) {
            $response['lastItem'] = true;
        } else {
            array_pop($list);
        }

        switch ($module) {
            case 'comments' :
                $response['res']['communication']['comments'] = $list;
                break;
            case 'blog' :
                $response['res']['blog']['post'][$objectId]['comments'] = $list;
                break;
            default:
                $response = array();
        }

        $this->ajaxResponseCode($response);
        return true;
    }

    public function deleteNotificationsAction()
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $playerId = $this->session->get(Player::IDENTITY)->getId();

        try {
            CommentsModel::instance()->setNotificationsDate($playerId);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            'delete' => array(
                'communication' => array(
                    'notifications'
                )
            ),
            'player' => array(
                'count' => array(
                    'notifications' => array(
                        'session' => 0,
                        'server'  => 0
                    )
                )
            )
        );

        $this->ajaxResponseCode($response,200);
        return true;
    }

    public function notificationsAction()
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $offset   = $this->request()->get('offset', NULL);
        $count    = $this->request()->get('count', self::$notificationsPerPage);
        $playerId = $this->session->get(Player::IDENTITY)->getId();

        try {
            $list = CommentsModel::instance()->getNotificationsList($playerId, $count + 1, $offset);

            $lastItem = true;
            $comments = array();
            foreach ($list as $commentData) {
                if (count($comments) == $count) {
                    $lastItem = false;
                    continue;
                }
                $comment    = array(
                    "user"       => array(
                        "id"   => $commentData['PlayerId'],
                        "img"  => $commentData['PlayerImg'],
                        "name" => $commentData['PlayerName'],
                    ),
                    "id"         => $commentData['Id'],
                    "comment_id" => $commentData['ParentId'],
                    "date"       => $commentData['Date'],
                    "text"       => $commentData['Text'],
                    "theme"      => $commentData['ParentText'],
                );
                $comments[] = $comment;
            }

            if (isset($comment["date"])) {
                CommentsModel::instance()->setNotificationsDate($playerId, $comment["date"]);
            }

            $response = array(
                'res'    => array(
                    'communication' => array(
                        'notifications' => $comments,
                    ),
                ),
                'player' => array(
                    'count' => array(
                        'notifications' => array(
                            "server"  => CommentsModel::instance()->getNotificationsCount($playerId),
                            "session" => "+".count($comments)
                        )
                    )
                )
            );

            if ($lastItem) {
                $response['lastItem'] = true;
            }

        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();

            return false;
        }

        $this->ajaxResponseCode($response,200);
        return true;
    }

    public function createAction($module = 'comments', $objectId = 0)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $playerId   = $this->session->get(Player::IDENTITY)->getId();
        $text       = $this->request()->post('text');
        $parentId   = $this->request()->post('comment_id', NULL);
        $toPlayerId = $this->request()->post('user_id', NULL);
        $image      = $this->request()->post('image', "");

        if ($parentId) {
            $parentComment = new Comment;
            $parentComment->setId($parentId)->fetch();
            $module   = $parentComment->getModule();
            $objectId = $parentComment->getObjectId();
        }

        $obj = new Comment;
        $obj->setPlayerId($playerId)
            ->setText($text)
            ->setParentId($parentId)
            ->setToPlayerId($toPlayerId)
            ->setModule($module)
            ->setObjectId($objectId);

        if ($image!="") {
            \Common::saveImageMultiResolution('',PATH_FILESTORAGE.'reviews/',$image, array(array(600),1),PATH_FILESTORAGE.'temp/'.$image);
            \Common::removeImageMultiResolution(PATH_FILESTORAGE.'temp/',$image);
        }

        $obj->setImage($image);

        try {
            $obj->create();
        } catch (\EntityException $e) {
            $this->ajaxResponseInternalError($e->getMessage());
            return false;
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            "message" => "message-comment-sent-success",
            "res" => array()
        );

        $this->ajaxResponseCode($response,201);
        return true;
    }

    public function likeAction($commentId)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $comment = new Comment;
        try {
            $comment->setId($commentId)->fetch();
        } catch (\EntityException $e) {
            $this->ajaxResponseNotFound($e->getMessage());
            return false;
        }

        $playerId = $this->session->get(Player::IDENTITY)->getId();
        $is_liked = CommentsModel::instance()->isLiked($commentId, $playerId);

        try {
            if (!$is_liked) {
                CommentsModel::instance()->like($commentId, $playerId);
            }
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            "res" => array(
                "communication" => array(
                    "comments" => array(
                        "$commentId" => array(
                            "like" => array(
                                "id" => $commentId,
                                "likes" => CommentsModel::instance()->getLikes($commentId),
                                "is_liked" => true
                            )
                        )
                    )
                )
            )
        );

        $this->ajaxResponseCode($response,200);
        return true;
    }

    public function dislikeAction($commentId)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $comment = new Comment;
        try {
            $comment->setId($commentId)->fetch();
        } catch (\EntityException $e) {
            $this->ajaxResponseNotFound($e->getMessage());
            return false;
        }

        $playerId = $this->session->get(Player::IDENTITY)->getId();
        $is_liked = CommentsModel::instance()->isLiked($commentId, $playerId);

        try {
            if ($is_liked) {
                CommentsModel::instance()->dislike($commentId, $playerId);
            }
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            "res" => array(
                "communication" => array(
                    "comments" => array(
                        "$commentId" => array(
                            "like" => array(
                                "id" => $commentId,
                                "likes" => CommentsModel::instance()->getLikes($commentId),
                                "is_liked" => false
                            )
                        )
                    )
                )
            )
        );

        $this->ajaxResponseCode($response,200);
        return true;
    }

}

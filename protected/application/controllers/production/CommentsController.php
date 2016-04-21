<?php
namespace controllers\production;
use \Application, \Player, \SettingsModel, \CommentsModel, \Comment;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class CommentsController extends \AjaxController
{

    static $notificationsPerPage;
    static $commentsPerPage;

    public function init()
    {
        self::$commentsPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('COMMENTS_PER_PAGE') ? : 10;
        self::$notificationsPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('NOTIFICATIONS_PER_PAGE') ? : 10;

        parent::init();
        $this->validateRequest();
        $this->authorizedOnly();
        $this->validateCaptcha();
    }

    public function itemAction($commentId)
    {

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

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function listAction($module = 'comments', $objectId = 0)
    {

        $playerId = $this->session->get(Player::IDENTITY)->getId();

        $count    = $this->request()->get('count', self::$commentsPerPage);
        $beforeId = $this->request()->get('before_id', NULL);
        $afterId  = $this->request()->get('after_id', NULL);

        try {
            $list = CommentsModel::instance()->getList($module, $objectId, $count+1, $beforeId, $afterId, 1, NULL, NULL, $playerId);
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

        if ($objectId>0) {
            foreach ($list as $key=>$value) {
                $list[$key]['object_id'] = $objectId;
            }
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

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function deleteNotificationsAction()
    {

        $playerId = $this->session->get(Player::IDENTITY)->getId();

        try {
            CommentsModel::instance()->setNotificationsDate($playerId);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            'player' => array(
                'count' => array(
                    'notifications' => array(
                        'server' => 0,
                        'local'  => 0
                    )
                )
            )
        );

        $this->ajaxResponseNoCache($response,200);
        return true;
    }

    public function notificationsAction()
    {

        // die(include 'res/GET/communication/_notifications');

        $offset   = $this->request()->get('offset', NULL);
        $count    = $this->request()->get('count', self::$notificationsPerPage);
        $playerId = $this->session->get(Player::IDENTITY)->getId();

        try {
            $list = CommentsModel::instance()->getNotificationsList($playerId, $count + 1, $offset);

            if (count($list)<=$count) {
                $lastItem = true;
            } else {
                $lastItem = false;
                array_pop($list);
            }

            $comments = array();
            foreach ($list as $commentData) {
                $comment    = array(
                    "user"       => array(
                        "id"   => $commentData['PlayerId'],
                        "img"  => $commentData['PlayerImg'],
                        "name" => $commentData['PlayerName'],
                        "ping" => $commentData['PlayerPing'],
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
                'cache'  => 'local',
                'res'    => array(
                    'communication' => array(
                        'notifications' => $comments,
                    ),
                ),
                'player' => array(
                    'count' => array(
                        'notifications' => array(
                            "server"  => CommentsModel::instance()->getNotificationsCount($playerId),
                            "local" => "+".count($comments)
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

        $this->ajaxResponseNoCache($response,200);
        return true;
    }

    public function createAction($module = 'comments', $objectId = 0)
    {

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


        $obj->setImage($image);

        try {

            if(CommentsModel::instance()->canPlayerPublish($playerId))
                $obj->setStatus(1);
            $obj->create();

            if ($image!="") {
                \Common::saveImageMultiResolution('', PATH_FILESTORAGE . 'reviews/', $image, array(array(600), 1), PATH_FILESTORAGE . 'temp/' . $image);
                \Common::removeImageMultiResolution(PATH_FILESTORAGE . 'temp/', $image);
            }


        } catch (\EntityException $e) {
            $this->ajaxResponseInternalError($e->getMessage());
            return false;
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        if($obj->getStatus() == 1) {

            $obj->fetch();
            $comments = array();
            $response = array();

            if (!$obj->getParentId()) {
                $comments[$obj->getId()] = $obj->export('JSON');
            } else {
                $comments[$obj->getParentId()]['answers'][$obj->getId()] = $obj->export('JSON');
            }

            switch ($module) {
                case 'comments' :
                    $response['res']['communication']['comments'] = $comments;
                    break;
                case 'blog' :
                    $response['res']['blog']['post'][$objectId]['comments'] = $comments;
                    break;
                default:
                    $response = array();
            }
        } else {
            $response = array(
                "message" => "message-comment-sent-success",
                "res" => array()
            );
        }

        $this->ajaxResponseNoCache($response,201);
        return true;
    }

    public function complainAction($commentId)
    {

        $playerId = $this->session->get(Player::IDENTITY)->getId();

        if(!in_array($playerId, (array) SettingsModel::instance()->getSettings('moderators')->getValue())){
            $this->ajaxResponseForbidden();
        }

        if(!($complain = $this->request()->post('complain', false)) || $complain  == ''){
            $this->ajaxResponseBadRequest('EMPTY_COMPLAIN_REASON');
        }

        $comment = new Comment;

        try {
            $comment->setId($commentId)->fetch();
        } catch (\EntityException $e) {
            $this->ajaxResponseNotFound($e->getMessage());
            return false;
        }

        if($comment->getModeratorId()) {
            $this->ajaxResponseBadRequest('COMMENT_ALREADY_MODERATED');
        }

        try {
            $comment
                ->setModeratorId($playerId)
                ->setStatus(0)
                ->setComplain($complain)
                ->update();
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $delete = array();

        if($comment->getParentId()){
            $delete[$comment->getParentId()]['answers'] = $commentId;
        } else {
            $delete = $commentId;
        }

        $response = array(
            "message" => 'comment-successfully-sent-to-moderation',
            "delete" => array(
                "communication" => array(
                    "comments" => array(
                        $delete
                    )
                )
            )
        );

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function likeAction($commentId)
    {

        $comment = new Comment;
        try {
            $comment->setId($commentId)->fetch();
        } catch (\EntityException $e) {
            $this->ajaxResponseNotFound($e->getMessage());
            return false;
        }

        $playerId = $this->session->get(Player::IDENTITY)->getId();
        $is_liked = CommentsModel::instance()->isLiked(array($commentId), $playerId);

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
                        $commentId => array(
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

        $this->ajaxResponseNoCache($response,200);
        return true;
    }

    public function dislikeAction($commentId)
    {

        $comment = new Comment;
        try {
            $comment->setId($commentId)->fetch();
        } catch (\EntityException $e) {
            $this->ajaxResponseNotFound($e->getMessage());
            return false;
        }

        $playerId = $this->session->get(Player::IDENTITY)->getId();
        $is_liked = CommentsModel::instance()->isLiked(array($commentId), $playerId);

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
                        $commentId => array(
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

        $this->ajaxResponseNoCache($response,200);
        return true;
    }

}

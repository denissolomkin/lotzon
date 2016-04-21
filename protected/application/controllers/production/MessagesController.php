<?php
namespace controllers\production;
use \Application, \Player, \SettingsModel, \MessagesModel, \Message;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class MessagesController extends \AjaxController
{
    static $messagesPerPage;

    public function init()
    {
        self::$messagesPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('MESSAGES_PER_PAGE') ? : 10;

        parent::init();
        $this->authorizedOnly(true);
        $this->validateCaptcha();
    }

    public function indexAction()
    {
        $this->validateRequest();

        $playerId = $this->session->get(Player::IDENTITY)->getId();
        $count    = $this->request()->get('count', self::$messagesPerPage);
        $offset   = $this->request()->get('offset', NULL);

        try {
            $list = MessagesModel::instance()->getLastTalks($playerId, $count+1, $offset);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            'cache' => 'session',
            'res' => array(
                'communication' => array(
                    'messages' => array()
                ),
            )
        );

        if (count($list)<=$count) {
            $response['lastItem'] = true;
        } else {
            array_pop($list);
        }

        foreach ($list as $id=>$message) {
            $export = $message->export('talk');
            $export['talk_id'] = $id;
            $toPlayerId        = $message->getToPlayerId();
            if ($toPlayerId==$playerId) {
                $export['author_id'] = $message->getPlayerId();
            } else {
                $export['author_id'] = $playerId;
                if (!isset($export['isUnread'])) {
                    $export['isUserRead'] = true;
                }
                unset($export['isUnread']);
            }
            $response['res']['communication']['messages'][$message->getId()] = $export;
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function listAction($userId)
    {
        $this->validateRequest();

        $playerId = $this->session->get(Player::IDENTITY)->getId();
        $count    = $this->request()->get('count', self::$messagesPerPage);
        $beforeId = $this->request()->get('before_id', NULL);
        $afterId  = $this->request()->get('after_id', NULL);
        $offset   = $this->request()->get('offset', NULL);

        try {
            $list = MessagesModel::instance()->getList($playerId, $userId, $count+1, $beforeId, $afterId, $offset);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            'res' => array(
                'users' => array(
                    "$userId" => array(
                        "messages" => array()
                    ),
                ),
            ),
        );

        if (count($list)<=$count) {
            $response['lastItem'] = true;
        } else {
            array_pop($list);
        }

        foreach ($list as $id=>$message) {
            $response['res']['users'][$userId]['messages'][$id] = $message->export('list');
        }

        MessagesModel::instance()->markRead($userId,$playerId);
        $response['player']['count']['messages'] = \MessagesModel::instance()->getStatusCount($playerId, 0);

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function markReadAction($userId)
    {
        $this->validateRequest();

        $response = array();
        $playerId = $this->session->get(Player::IDENTITY)->getId();

        try {
            MessagesModel::instance()->markRead($userId, $playerId);
            $response['player']['count']['messages'] = MessagesModel::instance()->getStatusCount($playerId, 0);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();

            return false;
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function createAction()
    {
        $this->validateRequest();

        $playerId   = $this->session->get(Player::IDENTITY)->getId();
        $text       = $this->request()->post('text');
        $toPlayerId = $this->request()->post('recipient_id', NULL);
        $image      = $this->request()->post('image', "");
        $admins     = array();

        if(\SettingsModel::instance()->getSettings('counters')->getValue('USER_REVIEW_DEFAULT'))
            array_push($admins, \SettingsModel::instance()->getSettings('counters')->getValue('USER_REVIEW_DEFAULT'));
        if(\SettingsModel::instance()->getSettings('counters')->getValue('USER_ORDERS_DEFAULT'))
            array_push($admins, \SettingsModel::instance()->getSettings('counters')->getValue('USER_ORDERS_DEFAULT'));

        $obj = new Message;
        $obj->setPlayerId($playerId)
            ->setToPlayerId($toPlayerId)
            ->setText(htmlspecialchars(strip_tags($text)));

        if (empty($admins) OR (!in_array($playerId, $admins) AND (!in_array($toPlayerId, $admins)))) {

            $toPlayer = new Player();
            $toPlayer
                ->setId($toPlayerId)
                ->setFriendship($playerId)
                ->initPrivacy();

            if (!$toPlayer->applyPrivacy('Message')) {
                $this->ajaxResponseForbidden('USER_DENIED_MESSAGE');
            }

            foreach (array('www', 'http', '@', '.ru') as $needle) {
                if (stristr($obj->getText(), $needle)) {
                    $obj->setApproval(0);
                    break;
                }
            }
        }

        if ($image!="") {
            \Common::saveImageMultiResolution('',PATH_FILESTORAGE.'messages/',$image, array(array(600),1),PATH_FILESTORAGE.'temp/'.$image);
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
        $player = new \Player;
        $player->setId($toPlayerId)->fetch();

        $response = array(
            'message' => 'message-successfully-sent',
            'cache'   => array(
                'communication-messages' => 'session',
            ),
            'captcha' => $this->activateCaptcha(),
            'res'     => array(
                'users'         => array(
                    $toPlayerId => array(
                        'messages' => array(),
                    ),
                ),
                'communication' => array(
                    'messages' => array(
                        $obj->getId() => array(
                            'user'      => $player->export('card'),
                            'id'        => $toPlayerId,
                            'author_id' => $playerId,
                            'date'      => $obj->getDate(),
                            'text'      => $obj->getText(),
                            'img'       => $obj->getImg,
                        ),
                    ),
                ),
            ),
            'delete'  => array(
                'communication' => array(
                    'messages' => array(
                        $toPlayerId => null,
                    ),
                ),
            ),
        );
        $response['res']['users'][$toPlayerId]['messages'][$obj->getId()] = $obj->export('list');

        $this->ajaxResponseNoCache($response,201);
        return true;
    }

    public function imageAction()
    {

        try {
            $imageName = uniqid() . ".png";
            \Common::saveImageMultiResolution('image',PATH_FILESTORAGE.'temp/', $imageName);
        } catch (\Exception $e) {
            $this->ajaxResponseInternalError();
        }

        $res = array(
            "imageName" => $imageName,
        );

        $this->ajaxResponseNoCache($res);

        return true;
    }

    public function imageDeleteAction()
    {
        $this->validateRequest();

        $image = $this->request()->delete('image', null);

        if (is_null($image)) {
            $this->ajaxResponseBadRequest();
        }

        try {
            \Common::removeImageMultiResolution(PATH_FILESTORAGE.'temp/',$image);
        } catch (\Exception $e) {
            $this->ajaxResponseInternalError();
        }

        $this->ajaxResponseNoCache(array());

        return true;
    }

}

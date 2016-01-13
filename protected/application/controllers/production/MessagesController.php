<?php
namespace controllers\production;
use \Application, \Player, \SettingsModel, \MessagesModel, \Message;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class MessagesController extends \AjaxController
{
    private $session;

    static $messagesPerPage;

    public function init()
    {
        self::$messagesPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('MESSAGES_PER_PAGE') ? : 10;

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

    public function indexAction()
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

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
            $response['res']['communication']['messages'][$message->getId()] = $message->export('talk');
        }

        $this->ajaxResponseCode($response);
        return true;
    }

    public function listAction($userId)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

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

        /*
        $first = array_shift($list);
        if (($offset === NULL) && (!is_null($first))) {
            //if (($first->getStatus() == 0)&&($first->getToPlayerId() == $playerId)) {
                $response['update'] = array(
                    "communication" => array(
                        "messages" => array(
                            $userId => array(
                                "isUnread" => false,
                                "id"       => $userId
                            )
                        )
                    )
                );
            //}
        }
        */

        MessagesModel::instance()->markRead($userId,$playerId);
        $response['player']['count']['messages'] = \MessagesModel::instance()->getStatusCount($playerId, 0);

        $this->ajaxResponseCode($response);
        return true;
    }

    public function markReadAction($userId)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $response = array();
        $playerId = $this->session->get(Player::IDENTITY)->getId();

        try {
            MessagesModel::instance()->markRead($userId, $playerId);
            $response['player']['count']['messages'] = MessagesModel::instance()->getStatusCount($playerId, 0);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();

            return false;
        }

        $this->ajaxResponseCode($response);
        return true;
    }

    public function createAction()
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $playerId   = $this->session->get(Player::IDENTITY)->getId();
        $text       = $this->request()->post('text');
        $toPlayerId = $this->request()->post('recipient_id', NULL);

        $obj = new Message;
        $obj->setPlayerId($playerId)
            ->setToPlayerId($toPlayerId)
            ->setText($text);

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
            "message" => "message-successfully-sent",
            'res'     => array(
                'users'         => array(
                    "$toPlayerId" => array(
                        "messages" => array()
                    ),
                ),
                'communication' => array(
                    'messages' => array(
                        array(
                            'user' => $player->export('card'),
                            'id'   => $toPlayerId,
                            'date' => $obj->getDate(),
                            'text' => $obj->getText(),
                            'img'  => $obj->getImg
                        )
                    )
                )
            ),
            'delete'  => array(
                'communication' => array(
                    'messages' => array(
                        $toPlayerId => NULL
                    )
                )
            )
        );
        $response['res']['users'][$toPlayerId]['messages'][$obj->getId()] = $obj->export('list');

        $this->ajaxResponseCode($response,201);
        return true;
    }

}

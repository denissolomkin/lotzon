<?php

namespace controllers\production;
use \Application, \Player, \EntityException, \CountriesModel, \SettingsModel, \StaticTextsModel, \WideImage, \EmailInvites, \EmailInvite, \LanguagesModel, \Common, \NoticesModel, \GamesSettingsModel, \GameSettingsModel, \ChanceGamesModel;
use \GeoIp2\Database\Reader;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');
Application::import(PATH_PROTECTED . 'external/wi/WideImage.php');

class FriendsController extends \AjaxController
{
    static $friendsPerPage;

    public function init()
    {
        self::$friendsPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('FRIENDS_PER_PAGE') ? : 10;
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

    public function listAction()
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $offset = $this->request()->get('offset');
        $count  = $this->request()->get('count', self::$friendsPerPage);
        $match  = $this->request()->get('match');

        $playerId = $this->session->get(Player::IDENTITY)->getId();

        try {
            $list = \FriendsModel::instance()->getList($playerId, $count, $offset, 1, $match);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            'res' => array(
            ),
        );

        if (!is_null($list)) {
            foreach ($list as $friend) {
                $response['res'][$friend['PlayerId']] = array(
                    'id'        => $friend['PlayerId'],
                    'img'       => $friend['PlayerImg'],
                    'name'      => $friend['PlayerName'],
                    'lotteries' => $friend['PlayerGamesPlayed'],
                    'money'     => $friend['PlayerMoney'],
                    'points'    => $friend['PlayerPoints'],
                    'ping'      => $friend['PlayerPing'],
                );
            }
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function requestsAction()
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $offset = $this->request()->get('offset');
        $count  = $this->request()->get('count', self::$friendsPerPage);

        $playerId = $this->session->get(Player::IDENTITY)->getId();

        try {
            $list = \FriendsModel::instance()->getList($playerId, $count+1, $offset, 0);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            'res' => array(
            ),
        );

        if (count($list)<=$count) {
            $response['lastItem'] = true;
        } else {
            array_pop($list);
        }

        if (!is_null($list)) {
            foreach ($list as $friend) {
                if ($friend['UserId']==$playerId)
                    continue;
                $response['res'][$friend['PlayerId']] = array(
                    'id'     => $friend['PlayerId'],
                    'user'   => array(
                        'id'   => $friend['PlayerId'],
                        'img'  => $friend['PlayerImg'],
                        'name' => $friend['PlayerName'],
                    ),
                    'date'   => $friend['ModifyDate'],
                    'status' => $friend['Status'],
                );
            }
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function chronicleAction()
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $offset = $this->request()->get('offset');
        $count  = $this->request()->get('count', self::$friendsPerPage);

        $playerId = $this->session->get(Player::IDENTITY)->getId();

        try {
            $list = \FriendsModel::instance()->getList($playerId, $count, $offset, NULL);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            'res' => array(
            ),
        );

        if (!is_null($list)) {
            foreach ($list as $friend) {
                switch ($friend['Status']) {
                    case 0:
                        $news = 'ADD_AS_FRIEND';
                        break;
                    case 1:
                        $news = 'BECAME_FRIENDS';
                        break;
                    case 2:
                        $news = 'REJECTED_REQUEST';
                        break;
                    default:
                        $news = 'ERROR';
                }
                $response['res'][$friend['PlayerId']] = array(
                    'id'     => $friend['PlayerId'],
                    'user'   => array(
                        'id'   => $friend['PlayerId'],
                        'img'  => $friend['PlayerImg'],
                        'name' => $friend['PlayerName'],
                    ),
                    'date'   => $friend['ModifyDate'],
                    'news'   => $news,
                );
            }
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function updateRequestAction($userId)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $status  = $this->request()->put('status');

        $playerId = $this->session->get(Player::IDENTITY)->getId();

        try {
            \FriendsModel::instance()->updateRequest($playerId, $userId, $status);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $player = new Player();
        $player->setId($userId)->fetch()->setFriendship($playerId);

        $response = array(
            'res' => array(
                'user' => array(
                    "$userId" => $player->export('info')
                )
            ),
            "delete" => array(
                "users" => array(
                    "requests" => array(
                        $userId
                    )
                )
            ),
            "player" => array(
                "count" => array(
                    "friends" => \FriendsModel::instance()->getStatusCount($playerId, 1),
                    "menu" => array(
                        "users" => array(
                            "requests" => \FriendsModel::instance()->getStatusCount($playerId, 0, true),
                        ),
                    ),
                ),
            ),
        );

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function deleteRequestAction($userId)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $playerId = $this->session->get(Player::IDENTITY)->getId();

        try {
            \FriendsModel::instance()->deleteRequest($playerId, $userId);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $player = new Player();
        $player->setId($userId)->fetch()->setFriendship($playerId);

        $response = array(
            'res' => array(
                'user' => array(
                    "$userId" => $player->export('info')
                )
            ),
            "player" => array(
                "count" => array(
                    "friends" => \FriendsModel::instance()->getStatusCount($playerId, 1),
                )
            )
        );

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function addRequestAction($userId)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $playerId = $this->session->get(Player::IDENTITY)->getId();

        try {
            $status = \FriendsModel::instance()->getStatus($userId, $playerId);
            if ($status === null) {
                \FriendsModel::instance()->addRequest($playerId, $userId);
            } else {
                switch ($status) {
                    case 0:
                        \FriendsModel::instance()->updateRequest($playerId, $userId, 1);
                        break;
                    case 1:
                        break;
                    case 2:
                        \FriendsModel::instance()->deleteRequest($userId, $playerId);
                        \FriendsModel::instance()->addRequest($playerId, $userId);
                        break;
                }
            }
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $player = new Player();
        $player->setId($userId)->fetch()->setFriendship($playerId);

        $response = array(
            'res' => array(
                'user' => array(
                    "$userId" => $player->export('info')
                )
            ),
            "player" => array(
                "count" => array(
                    "friends" => \FriendsModel::instance()->getStatusCount($playerId, 1),
                    "menu" => array(
                        "users" => array(
                            "requests" => \FriendsModel::instance()->getStatusCount($playerId, 0, true),
                        ),
                    ),
                ),
            ),
        );

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function removeAction($userId)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $playerId = $this->session->get(Player::IDENTITY)->getId();

        try {
            \FriendsModel::instance()->remove($playerId, $userId);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $player = new Player();
        $player->setId($userId)->fetch()->setFriendship($playerId);

        $response = array(
            'res' => array(
                'user' => array(
                    "$userId" => $player->export('info')
                )
            ),
            "player" => array(
                "count" => array(
                    "friends" => \FriendsModel::instance()->getStatusCount($playerId, 1),
                )
            )
        );

        $this->ajaxResponseNoCache($response);
        return true;
    }

}

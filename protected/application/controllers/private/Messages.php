<?php
namespace controllers\admin;

use \Application, \PrivateArea, \MessagesModel, \Message, \EntityException, \Session2, \Admin, \SettingsModel;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Messages extends PrivateArea
{
    public $activeMenu = 'messages';
    static $PER_PAGE;

    public function init()
    {
        parent::init();
        self::$PER_PAGE = SettingsModel::instance()->getSettings('counters')->getValue('MESSAGES_PER_ADMIN') ? : 10;

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {

        $page  = $this->request()->get('page', 1);
        $count = MessagesModel::instance()->getMessagesToApproveCount();
        $list  = MessagesModel::instance()->getMessagesToApprove(
            self::$PER_PAGE,
            $page == 1 ? 0 : self::$PER_PAGE * $page - self::$PER_PAGE
        );

        $pager = array(
            'page' => $page,
            'rows' => $count,
            'per_page' => self::$PER_PAGE,
            'pages' => 0,
        );

        $pager['pages'] = ceil($pager['rows'] / $pager['per_page']);

        $this->render('admin/' . $this->activeMenu, array(
            'title'       => 'Спам',
            'layout'      => 'admin/layout.php',
            'activeMenu'  => $this->activeMenu,
            'list'        => $list,
            'pager'       => $pager,
            'frontend'    => 'users',
        ));
    }

    public function listAction($playerId, $toPlayerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                foreach(MessagesModel::instance()->getList($playerId, $toPlayerId) as $id => $message)

                $response['data']['messages'][] = array(
                    'PlayerId' => $message->getPlayerId(),
                    'PlayerName' => $message->getPlayerName(),
                    'ToPlayerId' => $message->getToPlayerId(),
                    'Text' => $message->getText(),
                    'Image' => $message->getImage(),
                    'Date' => date('d.m.Y <b\r> H:i:s',$message->getDate())
                );

            } catch (ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
        $this->redirect('/private');
    }

    public function approveAction($id)
    {
        if ($this->request()->isAjax()) {

            $message = new Message();
            $message
                ->setId($id)
                ->fetch()
                ->setApproval(1);

            try {
                $message->update();
            } catch (EntityException $e) {
            }

            $response = array(
                'status' => 1,
                'message' => 'OK',
                'data' => array(
                    'count' => MessagesModel::instance()->getMessagesToApproveCount()
                ),
            );

            die(json_encode($response));
        }

        $this->redirect('/private');
    }


    public function deleteAction($id)
    {
        if ($this->request()->isAjax()) {

            $message = new Message();
            $message->setId($id)->fetch();

            try {
                $message->delete();
            } catch (EntityException $e) {
            }

            $response = array(
                'status' => 1,
                'message' => 'OK',
                'data' => array(
                    'count' => MessagesModel::instance()->getMessagesToApproveCount()
                ),
            );
            die(json_encode($response));
        }

        $this->redirect('/private');
    }
}
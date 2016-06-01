<?php
namespace controllers\admin;
use \Session2, \Application, \GameAppsModel, \SettingsModel, \Player, \Admin;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class ReferralsTop extends \PrivateArea
{
    public $activeMenu = 'referralstop';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {
        $referralsTop = \PlayersModel::instance()->getTopReferralsIncr();

        $this->render('admin/referralstop', array(
            'layout'       => 'admin/layout.php',
            'title'        => 'В топе рефералов',
            'activeMenu'   => $this->activeMenu,
            'referralsTop' => $referralsTop,
        ));
    }

    public function getPlayerAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            try {
                $player = new Player();
                $player->setId($playerId)->fetch();
            } catch (\EntityException $e) {

                $response['status']  = 0;
                $response['message'] = $e->getMessage();
            }

            $response['data'] = array(
                'Id'      => $player->getId(),
                'Avatar'  => $player->getAvatar(),
                'Nicname' => $player->getNicname(),
                'Utc'     => $player->getUtc(),
            );

            die(json_encode($response));
        }
        $this->redirect('/private');
    }

    public function createAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            try {
                $response['data'] = \PlayersModel::instance()->createTopReferralsIncr($this->request()->post());
            } catch (\ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');

    }

    public function updateAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            try {
                $response['data'] = \PlayersModel::instance()->updateTopReferralsIncr($this->request()->put());
            } catch (\ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');

    }


    public function deleteAction($id)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            try {
                $response['data'] = \PlayersModel::instance()->deleteTopReferralsIncr($id);
            } catch (\ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');

    }

}
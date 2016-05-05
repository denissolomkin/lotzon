<?php
namespace controllers\admin;
use \Session2, \Application, \GameAppsModel, \SettingsModel, \Player, \Admin;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class GameTop extends \PrivateArea
{
    public $activeMenu = 'gametop';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {
        $month = (strtotime($this->request()->get('month', null))?:mktime(0, 0, 0, date("n"), 1));
        $gameTop = GameAppsModel::instance()->getGameTop($month);
        $timezones = SettingsModel::instance()->getSettings('counters')->getValue('BOT_TIMEZONES');
        $onlineGames = array();

        foreach(\GameConstructorModel::instance()->getList()['online'] as $onlineGame)
            $onlineGames[$onlineGame->getId()]=$onlineGame->getTitle('default');

        $this->render('admin/gametop', array(
            'layout'             => 'admin/layout.php',
            'title'              => 'Наши в топе',
            'activeMenu'         => $this->activeMenu,
            'gameTop'            => $gameTop,
            'timezones'          => $timezones,
            'onlineGames'        => $onlineGames,
            'month'              => $month
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
            } catch (\EntityException $e){

                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            $response['data'] = array(
                'Id'=>$player->getId(),
                'Avatar'=>$player->getAvatar(),
                'Nicname'=>$player->getNicname(),
                'Utc'=>$player->getUtc()
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
                $response['data'] = GameAppsModel::instance()->createGameTop($this->request()->post());
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
                $response['data'] = GameAppsModel::instance()->updateGameTop($this->request()->put());
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
                $response['data'] = GameAppsModel::instance()->deleteGameTop($id);
            } catch (\ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');

    }

}
<?php
namespace controllers\admin;
use \Session2, \Application, \OnlineGamesModel, \SettingsModel, \Player, \Admin;

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
        //1441065600 1441065600
        $month = mktime(0, 0, 0, date("n"), 1);
        echo $month;
        if (ini_get('date.timezone')) {
            echo 'date.timezone: ' . ini_get('date.timezone');
        }
        $month = (strtotime($this->request()->get('month', null))?:mktime(0, 0, 0, date("n"), 1));
        $gameTop = OnlineGamesModel::instance()->getGameTop($month);
        $onlineGames = array();

        foreach(OnlineGamesModel::instance()->getList() as $onlineGame)
            $onlineGames[$onlineGame->getId()]=$onlineGame->getTitle('default');

        $this->render('admin/gametop', array(
            'layout'             => 'admin/layout.php',
            'title'              => 'Наши в топе',
            'activeMenu'         => $this->activeMenu,
            'gameTop'            => $gameTop,
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
                'Nicname'=>$player->getNicname()
            );

            die(json_encode($response));
        }

        $this->redirect('/private');

    }


    public function saveAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1, 
                'message' => 'OK',
                'data'    => array(),
            );

            try {
                $response['data'] = OnlineGamesModel::instance()->saveGameTop($this->request()->post());
            } catch (LotterySettingsException $e) {
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
                $response['data'] = OnlineGamesModel::instance()->deleteGameTop($id);
            } catch (LotterySettingsException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');

    }

}
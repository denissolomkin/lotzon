<?php
namespace controllers\admin;

use \Application, \PrivateArea, \Player, \PlayersModel, \ModelException, \LotteriesModel;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/PlayersModel.php');
Application::import(PATH_APPLICATION . '/model/models/LotteriesModel.php');
Application::import(PATH_APPLICATION . '/model/entities/Player.php');

class Users extends PrivateArea 
{
    public $activeMenu = 'users';

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $list = PlayersModel::instance()->getList();
        $count = PlayersModel::instance()->getPlayersCount();

        $this->render('admin/users', array(
            'title'      => 'Пользователи',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list'       => $list,
            'playersCount'  => $count,
        ));
    }

    public function statsAction($playerId) 
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $lotteries = LotteriesModel::instance()->getPlayerHistory($playerId);    
            
                foreach ($lotteries as &$lottery) {
                    $lottery['Date']  = date('d.m.Y', $lottery['Date']);
                }
                $response['data']['lotteries'] = $lotteries;    
            } catch (ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }
}
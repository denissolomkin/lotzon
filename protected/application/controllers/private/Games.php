<?php
namespace controllers\admin;

use \Application, \PrivateArea, \Config, \GameSettings, \OnlineGamesModel, \QuickGamesModel, \GameSettingsModel, \EntityException, \Admin, \Session2;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Games extends PrivateArea
{
    public $activeMenu = 'games';

    public function init()
    {
        parent::init();

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
    }

    public function indexAction()
    {
       $qgames = QuickGamesModel::instance()->getList();
       $ogames = OnlineGamesModel::instance()->getList();
       $games  = GameSettingsModel::instance()->getList();


        $this->render('admin/games', array(
            'title'      => 'Настройка игр',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'games'      => $games,
            'qgames'      => $qgames,
            'ogames'      => $ogames,
        ));
    }

    public function saveAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            $game = new GameSettings();
            $game->setKey($this->request()->post('key'))
                ->setTitle($this->request()->post('title'))
                ->setOptions($this->request()->post('options'))
                ->setGames($this->request()->post('games'));

            try {
                $game->update();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
    }
}
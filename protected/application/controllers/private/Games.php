<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \GamePublished, \GameConstructorModel, \GamesPublishedModel, \EntityException, \Admin, \Session2;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Games extends PrivateArea
{
    public $activeMenu = 'games';

    public function init()
    {
        parent::init();

        if (!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {
        $games  = GamesPublishedModel::instance()->getList();
        $qgames = GameConstructorModel::instance()->getList()['chance'];
        $ogames = GameConstructorModel::instance()->getList()['online'];

        $this->render('admin/games', array(
            'title'      => 'Конструктор игр',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'games'      => $games,
            'qgames'     => $qgames,
            'ogames'     => $ogames,
            'frontend'   => 'statictexts',
        ));
    }

    public function saveAction()
    {
        if ($this->request()->isAjax()) {
            $response      = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            $gamePublished = new GamePublished();
            $gamePublished->setKey($this->request()->post('key'))
                ->setOptions($this->request()->post('options'))
                ->setGames($this->request()->post('games'));

            try {
                $gamePublished->update();
            } catch (EntityException $e) {
                $response['status']  = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
    }
}
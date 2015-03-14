<?php
namespace controllers\admin;

use \Application, \PrivateArea, \Config, \ShopModel, \OnlineGame, \OnlineGamesModel, \EntityException, \Admin, \SupportedCountriesModel,  \Session2;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class OnlineGames extends PrivateArea
{
    public $activeMenu = 'ogames';

    public function init()
    {
        parent::init();

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
    }

    public function indexAction()
    {
       $games = OnlineGamesModel::instance()->getList();
       $langs=Config::instance()->langs;

        $this->render('admin/ogames', array(
            'title'      => 'Онлайн-игры',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'games'      => $games,
            'langs' => $langs,
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
            $post=$this->request()->post('game');
            $game = new OnlineGame();

            $game->setId($post['Id'])
                ->setKey($post['Key'])
                ->setTitle($post['Title'])
                ->setDescription($post['Description'])
                ->setModes($post['Prizes'])
                ->setOptions($post['Field'])
                ->setAudio($post['Audio'])
                ->setEnabled($post['Enabled']?true:false);

            try {
                $game->save();
                $response['data'] = array('Id'  => $game->getId());
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
    }
}
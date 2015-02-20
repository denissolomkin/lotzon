<?php
namespace controllers\admin;

use \Application, \PrivateArea, \NewsModel, \Config, \ShopModel, \QuickGame, \QuickGamesModel, \EntityException, \Admin, \Session2;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class QuickGames extends PrivateArea
{
    public $activeMenu = 'qgames';

    public function init()
    {
        parent::init();

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
    }

    public function indexAction()
    {   
       $shopItems = ShopModel::instance()->getAllItems(false);
       $games = QuickGamesModel::instance()->getGamesSettings();

        $this->render('admin/qgames', array(
            'title'      => 'Конструктор игр',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'shopItems'  => $shopItems,
            'games'      => $games,
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
            $game = new QuickGame();

            $game->setId($post['Id'])
                ->setTitle($post['Title'])
                ->setDescription($post['Description'])
                ->setPrizes($post['Prizes'])
                ->setField($post['Field'])
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
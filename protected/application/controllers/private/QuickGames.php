<?php
namespace controllers\admin;

use \Application, \PrivateArea, \LanguagesModel, \SettingsModel, \ShopModel, \QuickGame, \QuickGamesModel, \EntityException, \Admin,  \Session2;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class QuickGames extends PrivateArea
{
    public $activeMenu = 'qgames';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {   
        $shopItems = ShopModel::instance()->getAllItems(false);
        $games = QuickGamesModel::instance()->getGamesSettings();
        $langs = LanguagesModel::instance()->getList();
        $defaultLang = LanguagesModel::instance()->defaultLang();

        $this->render('admin/qgames', array(
            'title'      => 'Конструктор игр',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'shopItems'  => $shopItems,
            'games'      => $games,
            'langs'      => $langs,
            'defaultLang'=> $defaultLang,
            'frontend'      => 'admin/games_frontend.php',
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
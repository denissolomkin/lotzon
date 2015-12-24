<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \LanguagesModel, \GameConstructor, \GameConstructorModel, \EntityException, \Admin, \Session2;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class OnlineGames extends PrivateArea
{
    public $activeMenu = 'ogames';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {
       $games = GameConstructorModel::instance()->getList()['online'];
       $langs = LanguagesModel::instance()->getList();
       $defaultLang = LanguagesModel::instance()->defaultLang();

        $this->render('admin/ogames', array(
            'title'      => 'Онлайн-игры',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'games'      => $games,
            'langs'      => $langs,
            'defaultLang'=> $defaultLang,
            'frontend'   => 'admin/games_frontend.php',
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
            $game = new GameConstructor();

            $game->setId($post['Id'])
                ->setKey($post['Key'])
                ->setTitle($post['Title'])
                ->setDescription($post['Description'])
                ->setModes($post['Prizes'])
                ->setOptions($post['Field'])
                ->setAudio($post['Audio'])
                ->setEnabled($post['Enabled']?true:false)
                ->setType('online');

            try {
                $game->update();
                $response['data'] = array('Id'  => $game->getId());
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
    }
}
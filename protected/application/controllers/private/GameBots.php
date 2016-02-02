<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \Session2, \Admin, \WideImage, \PlayersModel;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class GameBots extends PrivateArea
{
    public $activeMenu = 'gamebots';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {

        $bots = \GamePlayersModel::instance()->getAvailableBots();
        $bot = (object)$bots[array_rand($bots)];
        echo count($bots);
        var_dump($bot->id);
        $ids = PlayersModel::instance()->getAvailableIds();
        if(is_array($list = SettingsModel::instance()->getSettings('gameBots')->getValue()))
            $ids = array_diff($ids,array_keys($list));

        $this->render('admin/gamebots', array(
            'title'      => 'Боты для игры',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list'       => $list,
            'ids'        => $ids,
        ));
    }

    public function saveAction()
    {
        if($this->request()->post('bots'))
            SettingsModel::instance()->getSettings('gameBots')->setValue($this->request()->post('bots'))->create();

        $this->redirect('/private/gamebots');
    }

    public function uploadPhotoAction()
    {

        $image = WideImage::loadFromUpload('image');
        $image = $image->resize(\Player::AVATAR_WIDTH, \Player::AVATAR_HEIGHT);
        //$image = $image->crop("center", "center", \Player::AVATAR_WIDTH, \Player::AVATAR_HEIGHT);

        $id=$this->request()->post('Id');
        $imageName = ($this->request()->post('imageName')?: uniqid() . ".jpg");


        $saveFolder = PATH_FILESTORAGE . 'avatars/' . (ceil($id / 100)) . '/';

        if (!is_dir($saveFolder)) {
            mkdir($saveFolder, 0777);
        }

        $image->saveToFile($saveFolder . $imageName, 100);

        $data = array(
            'imageName' => $imageName,
            'imageWebPath' => '/filestorage/avatars/'.(ceil($id / 100)) . '/' . $imageName,
        );

        die(json_encode($data));
    }

}
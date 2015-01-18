<?php
namespace controllers\admin;

use \Application, \PrivateArea, \Config, \Session2, \Admin, \WideImage;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class GameBots extends PrivateArea
{
    public $activeMenu = 'gamebots';

    public function init()
    {
        parent::init();

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
    }

    public function indexAction()
    {
        $list    = Config::instance()->gameBots;

        $this->render('admin/gamebots', array(
            'title'      => 'Боты для игры',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list'       => $list,
        ));
    }

    public function saveAction()
    {
        if($this->request()->post('bots'))
            Config::instance()->save('gameBots',$this->request()->post('bots'));

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
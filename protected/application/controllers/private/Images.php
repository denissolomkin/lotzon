<?php
namespace controllers\admin;
use \PrivateArea, \Application, \Session2, \Admin, \EntityException, \SettingsModel;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_PROTECTED . '/external/wi/WideImage.php');

class Images extends PrivateArea
{
    public $activeMenu = 'images';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {
        $folder = $this->request()->get('folder', false);
        $webDir="tpl/img/".($folder?$folder.'/':'');
        $saveDir = PATH_ROOT.$webDir;
        if($openDir=opendir($saveDir))
        {
            $images=array();
            while(($file=readdir($openDir)) !== false)
                if($file != "." && $file != "..") {
                    if(is_file($saveDir.'/'.$file)){
                        $size = getimagesize ($saveDir.$file);
                        if($size[1]>400)
                            $images[]=array('name'=>$file,'size'=>($size? array($size[0],$size[1]):false));
                        else
                            array_unshift($images,array('name'=>$file,'size'=>($size? array($size[0],$size[1]):false)));
                        } else {
                        $folders[]=array('name'=>$file);
                    }
                }
        }

        $this->render('admin/images', array(
            'title'      => 'Изображения',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'images'  => $images,
            'folders'  => $folders,
            'webDir'  => '/'.$webDir,
            'curDir' => $folder,
        ));
    }


    public function deleteAction()
    {
        if ($this->request()->isAjax()) {
            $folder = $this->request()->get('folder', false);
            $imageName = $this->request()->get('image', false);
            $saveDir="tpl/img/".($folder?$folder.'/':'');

            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'delete'    => $saveDir. $imageName,
            );

            try {
                unlink(PATH_ROOT.$saveDir. $imageName);
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        } 

        $this->redirect('/private/'); 
    }

    public function audioAction()
    {
        $saveDir = "tpl/audio/";

        try {
            move_uploaded_file($_FILES['audio']['tmp_name'], PATH_ROOT . $saveDir . basename($_FILES['audio']['name']));
        } catch (EntityException $e) {
            $response['status'] = 0;
            $response['message'] = $e->getMessage();
        }

        $data = array(
            'audioName' => basename($_FILES['audio']['name']),
        );

        die(json_encode($data));
    }

    public function uploadAction()
    {
            $folder = $this->request()->get('folder', false);
            $imageName = trim($this->request()->post('name', false)) ?: basename($_FILES['image']['name']);
            $saveDir = "tpl/img/" . ($folder ? $folder . '/' : '');

            try {
                move_uploaded_file($_FILES['image']['tmp_name'], PATH_ROOT . $saveDir . $imageName);
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            $size = getimagesize(PATH_ROOT . $saveDir . $imageName);
            $data = array(
                'imageName' => $imageName,
                'imageWebPath' => $saveDir . $imageName,
                'imageWidth' => $size[0],
                'imageHeight' => $size[1],
            );

            die(json_encode($data));
    }

}
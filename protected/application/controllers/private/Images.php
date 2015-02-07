<?php
namespace controllers\admin;
use \PrivateArea, \Application, \Session2, \Admin, \WideImage, \EntityException, \Config;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_PROTECTED . '/external/wi/WideImage.php');

class Images extends PrivateArea
{
    public $activeMenu = 'images';

    public function init()
    {
        parent::init();

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
    }

    public function indexAction()
    {
        $folder = $this->request()->get('folder', false);
        $webDir="tpl/img/".($folder?$folder.'/':''); # папка, которую нужно прочитать
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
/*
                    echo "<div style='display:inline-table;position: relative;margin:5px;'>".
                        ($size?"<div style='position:absolute;margin: 1px;padding:5px;background: rgba(255,255,255,0.5);border-radius: 5px 0 0 0;font:13px/13px Handbook-regular;'>{$size[0]}x{$size[1]}</div>":"").
                        "<div style='position:absolute;right:0;top:0;padding:5px;'>
                        <button class='btn btn-xs btn-info notes-trigger' data-type='Note' data-id='20028'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span></button></div>".
                        "<div style='position:absolute;right:0;bottom:0;margin: 1px;padding:5px;background: rgba(255,255,255,0.5);border-radius: 0 0 5px 0;font:13px/13px Handbook-regular;'>{$file}</div>".
                        "<img src='{$webDir}{$file}' style='max-width:500px;max-height: 100px;min-height: 100px;padding:5px;border:1px solid gray;border-radius: 5px;'/></div>";
*/
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
            /*

                    $image = WideImage::loadFromUpload('image');
                    $image->saveToFile(PATH_ROOT.$saveDir . $imageName, 100);
            */
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
<?php
namespace controllers\admin;
use \PrivateArea, \Application, \Session2, \Admin, \SettingsModel;


Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Partners extends PrivateArea
{
    public $activeMenu = 'partners';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {
        $folder='partner-expl';
        $webDir="tpl/img/{$folder}/";
        $saveDir = PATH_ROOT.$webDir;
        $images=array();
        if($openDir=opendir($saveDir))
            while(($file=readdir($openDir)) !== false)
                if($file != "." && $file != "..")
                    if(is_file($saveDir.'/'.$file)){
                        $size = getimagesize ($saveDir.$file);
                        if($size[1]>400)
                            $images[]=array('name'=>$file,'size'=>($size? array($size[0],$size[1]):false));
                        else
                            array_unshift($images,array('name'=>$file,'size'=>($size? array($size[0],$size[1]):false)));
                    }

        $list = SettingsModel::instance()->getSettings($this->activeMenu)->getValue();
        $this->render('admin/partners', array(
            'title'      => 'Партнеры',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list'       => $list,
            'images'     => $images,
            'webDir'  => '/'.$webDir,
            'curDir' => $folder,
        ));
    }

    public function saveAction()
    {
        if($partners=$this->request()->post('partners')) {
            $partners=array_filter($partners);
            SettingsModel::instance()->getSettings($this->activeMenu)->setValue($partners)->create();
        }

        $this->redirect('/private/partners');
    }


}
<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \Session2, \Admin;
use Ratchet\Wamp\Exception;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Moderators extends PrivateArea
{
    public $activeMenu = 'moderators';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {

        $list = SettingsModel::instance()->getSettings($this->activeMenu)->getValue();
        $this->render('admin/'.$this->activeMenu, array(
            'title'      => 'Модераторы',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list' => $list,
        ));
    }

    public function saveAction()
    {

            try {

                $counters = $this->request()->post($this->activeMenu);
                if($counters) {
                    $counters = explode(',', $counters);
                    in_array(1, $counters);
                }

            } catch(\Exception $e){
                $response = array(
                    'status'  => 0,
                    'message' => 'ERROR',
                    'res'    => null
                );

                die(json_encode($response));
            }


            SettingsModel::instance()->getSettings($this->activeMenu)->setValue($counters)->create();


        $this->redirect('/private/'.$this->activeMenu);
    }
}
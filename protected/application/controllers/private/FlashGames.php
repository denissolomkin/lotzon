<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \Session2, \Admin, \WideImage, \PlayersModel;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class FlashGames extends PrivateArea
{
    static $MENU = 'fgames';
    static $KEY  = 'flashGames';

    public function init()
    {
        parent::init();

        if (!array_key_exists(self::$MENU, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {

        $list = SettingsModel::instance()->getSettings(self::$KEY)->getValue();

        $this->render('admin/' . self::$MENU, array(
            'title'      => 'Флеш игры',
            'layout'     => 'admin/layout.php',
            'activeMenu' => self::$MENU,
            'list'       => $list,
        ));
    }

    public function saveAction()
    {
        SettingsModel::instance()->getSettings(self::$KEY)->setValue($this->request()->post('fgames', array()))->create();
        $this->redirect('/private/' . self::$MENU);
    }

}
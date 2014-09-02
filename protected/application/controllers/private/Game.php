<?php
namespace controllers\admin;
use \Session, \Application, \EntityException;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Game extends \PrivateArea
{
    public $activeMenu = 'game';

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->render('admin/game', array(
            'layout' => 'admin/layout.php',
            'title'  => 'Game settings',
            'activeMenu' => $this->activeMenu,
        ));
    }

    public function saveAction()
    {
        die(json_encode(array('status' => 1, 'message' => 'Some critical error occured')));
    }

}
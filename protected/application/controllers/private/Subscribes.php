<?php
namespace controllers\admin;
use \DB;

\Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Subscribes extends \PrivateArea
{
    public $activeMenu = 'subscribes';

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $count = DB::Connect()->query("SELECT COUNT(*) FROM `Subscribes`")->fetchColumn(0);
        $list = DB::Connect()->query("SELECT * FROM `Subscribes` ORDER BY `DateSubscribed` DESC")->fetchAll();
        $this->render('admin/subscribes', array(
            'layout'     => 'admin/layout.php',
            'title'      => 'Заявки',
            'activeMenu' => 'subscribes',
            'count'      => $count,
            'list'       => $list,
        ));
    }

}
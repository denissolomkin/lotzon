<?php
namespace controllers\admin;

\Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class ComingSoon extends \PrivateArea
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->render('admin/cs', array(
            'layout'             => 'admin/layout.php',
            'title'              => 'ComingSoon',
            'activeMenu'        => '',
        ));
    }

}
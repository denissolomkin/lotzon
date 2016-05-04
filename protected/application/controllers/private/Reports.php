<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \Session2, \Admin, \ReportsModel, \AdminModel;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . 'model/models/AdminModel.php');

class Reports extends PrivateArea
{

    public function init()
    {
        parent::init();
    }

    public function indexAction($identifier)
    {

        $activeMenu = 'reports/'.$identifier;

        if(!array_key_exists($activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

        $dateFrom   = strtotime($this->request()->get('dateFrom', date('Y-m-01', time())));
        $dateTo     = strtotime($this->request()->get('dateTo', date('Y-m-d',time())).' 23:59:59');
        $args       = $this->request()->get('args', null);
        $reports    = ReportsModel::instance()->{('get'.$identifier)}($dateFrom,$dateTo,$args);

        $this->render('admin/reports', array(
            'title'      => Admin::$PAGES['Отчеты']['pages'][$activeMenu]['name'],
            'layout'     => 'admin/layout.php',
            'identifier' => $identifier,
            'reports'    => $reports,
            'activeMenu' => $activeMenu,
            'args'       => $args,
            'admins'     => AdminModel::getList(),
            'dateTo'     =>  date('Y-m-d',$dateTo),
            'dateFrom'   =>  date('Y-m-d',$dateFrom),
        ));
    }

}
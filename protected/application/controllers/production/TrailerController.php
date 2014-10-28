<?php
namespace controllers\production;
use \Application, \Config, \DB;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class TrailerController extends \AjaxController
{
    public function init() {}

    public function indexAction()
    {
        $this->render('production/trailer', array(
            'countdown' => strtotime("03.11.2014 10:00:00") - time(),
            'layout' => false,
        ));
    }

    public function subscribeAction() {
        $email = $this->request()->post('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->ajaxResponse(array(), 0, 'INVALID_EMAIL');
        }

        $exists = DB::Connect()->query(sprintf("SELECT Id FROM `Subscribes` WHERE `Email` = %s", DB::Connect()->quote($email)))->fetchColumn(0);
        if ($exists) {
            $this->ajaxResponse(array(), 0, 'ALREADY_SUBSCRIBED');
        }

        DB::Connect()->query(vsprintf("INSERT INTO `Subscribes` (`Email`, `DateSubscribed`) VALUES (%s, %s)", array(
            DB::Connect()->quote($email),
            DB::Connect()->quote(time()),
        )));

        $this->ajaxResponse(array());
    }
}
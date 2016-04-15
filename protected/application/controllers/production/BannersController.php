<?php
namespace controllers\production;
use \Application, \Banner;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class BannersController extends \AjaxController
{

    public function init()
    {
        parent::init();
        $this->validateRequest();
        $this->authorizedOnly(true);
    }

    public function indexAction($device, $location, $page = null)
    {

        $page = $page ?: $this->request()->get('page');

        try {

            $banner  = new Banner;

            $render = $banner
                ->setDevice($device)
                ->setLocation($location)
                ->setPage($page)
                ->setCountry($this->player->getCountry())
                ->setTemplate($device)
                /*->setGroup(ucfirst($device).ucfirst($location))
                ->setKey($device)*/
                ->random()
                ->render();

        } catch (\Exception $e) {
            echo $e->getMessage();
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            'res' => $render
        );

        $this->ajaxResponseNoCache($response);
        return true;
    }


}

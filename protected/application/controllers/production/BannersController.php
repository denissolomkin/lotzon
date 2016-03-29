<?php
namespace controllers\production;
use \Application, \Player, \SettingsModel, \Banner;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class BannersController extends \AjaxController
{

    public function init()
    {
        parent::init();
        $this->validateRequest();
        $this->authorizedOnly();
    }

    public function indexAction($device, $location, $page = null)
    {

        $player = $this->session->get(Player::IDENTITY);
        $page = $page ?: $this->request()->get('page');

        try {

            $banner  = new Banner;

            $render = $banner
                ->setDevice($device)
                ->setLocation($location)
                ->setPage($page)
                ->setCountry($player->getCountry())
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

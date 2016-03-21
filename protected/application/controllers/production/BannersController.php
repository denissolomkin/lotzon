<?php
namespace controllers\production;
use \Application, \Player, \SettingsModel, \Banner;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class BannersController extends \AjaxController
{
    private $session;

    public function init()
    {

        $this->session = new Session();
        parent::init();
    }

    private function authorizedOnly()
    {
        if (!$this->session->get(Player::IDENTITY) instanceof Player) {
            $this->ajaxResponseUnauthorized();
            return false;
        }
        $this->session->get(Player::IDENTITY)->markOnline();
        return true;
    }

    public function indexAction($device, $location)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();
        $player = $this->session->get(Player::IDENTITY);

        try {

            $banner  = new Banner;

            $render = $banner
                ->setGroup(ucfirst($device).ucfirst($location))
                ->setCountry($player->getCountry())
                ->setTemplate($device)
                ->setKey($device)
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

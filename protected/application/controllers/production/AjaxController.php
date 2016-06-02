<?php

use Symfony\Component\HttpFoundation\Session\Session;

class AjaxController extends \SlimController\SlimController
{
    protected $session;
    protected $player;

    public function __construct(\Slim\Slim &$app)
    {
        parent::__construct($app);
        $this->init();
    }

    public function init()
    {
        $this->session = new Session();
    }

    public function ajaxResponse(array $data, $status = 1, $message = 'OK')
    {
        $response = array(
            'status'  => $status,
            'message' => \StaticTextsModel::instance()->setLang($this->lang)->getText($message),
            'res'     => $data,
        );

        die(json_encode($response));
    }

    protected function isAuthorized($initPlayer = false)
    {
        if (!$this->session->get(Player::IDENTITY) instanceof Player) {
            return false;
        } else if ($initPlayer){
            $this->player = $this->session->get(Player::IDENTITY);
        }
        $this->validatePlayer();
        return true;
    }

    protected function authorizedOnly($initPlayer = false)
    {
        if (!$this->isAuthorized($initPlayer)) {
            $this->ajaxResponseUnauthorized();
            return false;
        }

        return true;
    }

    protected function validatePlayer()
    {

        /*
         * patch for old Player Entity in Memcache session
         */

        try {

            if($this->session->get(Player::IDENTITY)->getVersion() !== 5)
                throw(new \Exception);

        } catch (\Exception $e) {
            $playerId = $this->session->get(Player::IDENTITY)
                ->fetch()
                ->getId();
            $this->player = new Player();
            $this->player
                ->setId($playerId)
                ->fetch()
                ->initDates()
                ->initPrivacy()
                ->initCounters()
                ->initAccounts();

            $this->player->updateSession();
        }
    }

    protected function validateRequest()
    {

        if (!$this->request()->isAjax()) {
            $this->session->set('page', $this->request()->getResourceUri());
            $this->redirect('/');
        } else {
            return true;
        }
    }

    protected function validateLogout()
    {

        if (\LogoutModel::instance()->fetch($this->session->get(Player::IDENTITY))) {
            session_destroy();
            $this->ajaxResponseUnauthorized();
            return false;
        }

        return true;
    }

    protected function validateCaptcha()
    {

        $captcha = \SettingsModel::instance()->getSettings('captcha')->getValue();

        if(isset($captcha['Enabled']) && $captcha['Enabled']) {
            if (\CaptchaModel::instance()->fetch($this->session->get(Player::IDENTITY))) {
                $this->ajaxResponseLocked();
                return false;
            }
        }

        return true;
    }

    public function ajaxResponseCode(array $data, $code = 200)
    {
        http_response_code($code);
        die(json_encode($data));
    }

    public function ajaxResponseNoCache(array $data, $code = 200)
    {

        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: -1"); // Proxies.
        http_response_code($code);
        die(json_encode($data));
    }

    public function ajaxResponseUnauthorized()
    {
        $this->ajaxResponseNoCache(array(), 401);
    }

    public function ajaxResponseInternalError($message = NULL)
    {
        if ($message) {
            $response = array(
                'message' => $message
            );
        } else
            $response = array();
        $this->ajaxResponseNoCache($response, 500);
    }

    public function ajaxResponseBadRequest($message = NULL)
    {
        if ($message) {
            $response = array(
                'message' => $message
            );
        } else
            $response = array();
        $this->ajaxResponseNoCache($response, 400);
    }

    public function ajaxResponseForbidden($message = NULL)
    {
        if ($message) {
            $response = array(
                'message' => $message
            );
        } else
            $response = array();
        $this->ajaxResponseNoCache($response, 403);
    }

    public function ajaxResponseNotFound($message = NULL)
    {
        if ($message) {
            $response = array(
                'message' => $message
            );
        } else
            $response = array();
        $this->ajaxResponseNoCache($response, 404);
    }

    public function ajaxResponseLocked($message = NULL)
    {
        if ($message) {
            $response = array(
                'message' => $message
            );
        } else
            $response = array();
        $this->ajaxResponseNoCache($response, 423);
    }
}

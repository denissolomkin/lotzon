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

    protected function authorizedOnly($initPlayer = false)
    {

        if (!$this->session->get(Player::IDENTITY) instanceof Player) {
            $this->ajaxResponseUnauthorized();
            return false;

        } else if ($initPlayer){
            $this->player = $this->session->get(Player::IDENTITY);
        }

        /* todo delete
        patch for old Player Entity in Memcache sessions
        delete after week from April 18 or after drop Memcache
        */
        try {

            $this->session->get(Player::IDENTITY)->getAccounts();

        } catch (\Exception $e) {
            $this->session->get(Player::IDENTITY)->fetch();
            $playerId = $this->session->get(Player::IDENTITY)->getId();
            $this->player = new Player();
            $this->player
                ->setId($playerId)
                ->fetch()
                ->initDates()
                ->initPrivacy()
                ->initCounters()
                ->initAccounts();
            $this->session->set(Player::IDENTITY, $this->player);
        }

        return true;
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

    protected function activateCaptcha()
    {

        $captcha = \SettingsModel::instance()->getSettings('captcha')->getValue();

        if(isset($captcha['Enabled']) && $captcha['Enabled']) {

            $time = $this->player->getCounters('CaptchaTime');

            if (isset($captcha['Settings']) && is_array($captcha['Settings'])) {
                foreach ($captcha['Settings'] as $term) {
                    if ((!strlen($term['Min']) || $time > $term['Min']) AND (!strlen($term['Max']) || $time <= $term['Max'])) {
                        if ($term['Rand'] && !rand(0, $term['Rand'] - 1)) {
                            \CaptchaModel::instance()->create($this->player);
                            return true;
                        }
                    }
                }
            }
        }

        return false;
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

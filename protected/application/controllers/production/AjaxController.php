<?php

use Symfony\Component\HttpFoundation\Session\Session;

class AjaxController extends \SlimController\SlimController
{
    protected $session;

    public function __construct(\Slim\Slim &$app)
    {
        parent::__construct($app);
        $this->init();
    }

    public function init()
    {
        $this->session = new Session();
        $this->validateRequest();
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

    protected function authorizedOnly()
    {
        if (!$this->session->get(Player::IDENTITY) instanceof Player) {
            $this->ajaxResponseUnauthorized();
            return false;
        }

        $this->session->get(Player::IDENTITY)->markOnline();

        return true;
    }

    protected function validateRequest()
    {
        if (!$this->request()->isAjax()) {
            $session = new Session();
            $session->set('page', $this->request()->getResourceUri());
            $this->redirect('/');
        } else
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
        $this->ajaxResponseCode(array(), 401);
    }

    public function ajaxResponseInternalError($message = NULL)
    {
        if ($message) {
            $response = array(
                'message' => $message
            );
        } else
            $response = array();
        $this->ajaxResponseCode($response, 500);
    }

    public function ajaxResponseBadRequest($message = NULL)
    {
        if ($message) {
            $response = array(
                'message' => $message
            );
        } else
            $response = array();
        $this->ajaxResponseCode($response, 400);
    }

    public function ajaxResponseForbidden($message = NULL)
    {
        if ($message) {
            $response = array(
                'message' => $message
            );
        } else
            $response = array();
        $this->ajaxResponseCode($response, 403);
    }

    public function ajaxResponseNotFound($message = NULL)
    {
        if ($message) {
            $response = array(
                'message' => $message
            );
        } else
            $response = array();
        $this->ajaxResponseCode($response, 404);
    }
}

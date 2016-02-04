<?php

class AjaxController extends \SlimController\SlimController
{

    public function __construct(\Slim\Slim &$app)
    {
        parent::__construct($app);
        $this->init();
    }

    public function init()
    {
    }

    public function validRequest()
    {
        return $this->request()->isAjax();
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

    protected function validateRequest()
    {
        if (!$this->request()->isAjax())
            die(\StaticTextsModel::instance()->setLang($this->lang)->getText('NONE_AJAX_REQUEST_DENIED'));
        else
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

<?php

class AjaxController extends \SlimController\SlimController {

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
            'status'    => $status,
            'message'   => \StaticTextsModel::instance()->setLang($this->lang)->getText($message),
            'res'       => $data,
        );

        die(json_encode($response));
    }
}
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
        if (!empty(Config::instance()->errorMessages[$message]))
        {
            $message = Config::instance()->errorMessages[$message];
        }
        $response = array(
            'status'    => $status,
            'message'   => $message,
            'res'       => $data,
        );

        die(json_encode($response));
    }
}
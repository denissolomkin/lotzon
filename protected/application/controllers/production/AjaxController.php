<?php

class AjaxController extends \SlimController\SlimController {

    public function validRequest()
    {
        return $this->request()->isAjax();
    }

    public function ajaxResponse(array $data, $status = 1, $message = 'OK')
    {
        $response = array(
            'status'    => $status,
            'message'   => $message,
            'res'       => $data,
        );

        die(json_encode($response));
    }
}
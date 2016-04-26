<?php
namespace controllers\production;
use \Application;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class ImagesController extends \AjaxController
{
    public function init()
    {

        parent::init();

        $this->authorizedOnly(true);
        $this->validateCaptcha();

    }

    public function messageAction()
    {

        try {
            $imageName = uniqid() . ".png";
            \Common::saveImageMultiResolution('image', PATH_FILESTORAGE . 'temp/', $imageName);
        } catch (\Exception $e) {
            $this->ajaxResponseInternalError();
        }

        $res = array(
            "imageName" => $imageName,
        );

        $this->ajaxResponseNoCache($res);

        return true;
    }

    public function avatarAction()
    {

        try {
            $imageName = $this->player->uploadAvatar();
        } catch (\Exception $e) {
            $this->ajaxResponseInternalError($e->getMessage());
        }

        $res = array(
            "player"  => array(
                "img" => $imageName,
            )
        );

        $this->ajaxResponseNoCache($res);

        return true;
    }

}

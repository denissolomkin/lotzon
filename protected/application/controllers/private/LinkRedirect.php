<?php
namespace controllers\admin;

use \Application, \PrivateArea, \LinkRedirectModel, \LinkRedirect, \EntityException;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/LinkRedirectModel.php');
Application::import(PATH_APPLICATION . '/model/entities/LinkRedirect.php');


class LinkRedirectController extends PrivateArea
{
    public $activeMenu = 'linkredirect';

    public function getLinkAction()
    {
        $this->render('admin/linkredirect', array(
            'title'      => 'Link Redirect',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu
        ));
    }

    public function postLinkAction()
    {
        $link = $this->request()->post('link','');

        if ($this->request()->isAjax()) {
            $response = array();
            try {
                $uin = LinkRedirectModel::instance()->getUin($link);
                if ($uin === NULL) {
                    $linkRedirect = new LinkRedirect();
                    $uin          = $linkRedirect->setLink($link)->setUin(uniqid())->create()->getUin();
                }
                $response = array(
                    'status'   => 1,
                    'message'  => 'OK',
                    'data'     => $uin
                );
            } catch (EntityException $e) {
                $response['status']  = 0;
                $response['message'] = $e->getMessage();
            }
            die(json_encode($response));
        }
    }

}

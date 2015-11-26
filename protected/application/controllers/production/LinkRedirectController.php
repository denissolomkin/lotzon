<?php

namespace controllers\production;
use \Application, \LinkRedirect;
Application::import(PATH_APPLICATION . 'model/entities/LinkRedirect.php');


class LinkRedirectController extends \SlimController\SlimController
{

    public function getLinkAction($uin = '')
    {
        $linkRedirect = new LinkRedirect();
        $link = $linkRedirect->setUin($uin)->fetch()->getLink();
        $this->redirect($link);
    }

}

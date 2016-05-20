<?php
namespace controllers\production;
use \Application, \Player, \SettingsModel, \CommentsModel, \Comment;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class CommentsControllerMixed extends \AjaxController
{
    static $commentsPerPage;

    public function init()
    {
        self::$commentsPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('COMMENTS_PER_PAGE') ? : 10;

        parent::init();
        $this->validateRequest();
    }

    public function listAction($module = 'comments', $objectId = 0)
    {
        if ($this->isAuthorized(true)) {
            $this->validateLogout();
            $this->validateCaptcha();
            $playerId = $this->session->get(Player::IDENTITY)->getId();
        } else {
            $playerId = NULL;
        }

        $count    = $this->request()->get('count', self::$commentsPerPage);
        $beforeId = $this->request()->get('before_id', NULL);
        $afterId  = $this->request()->get('after_id', NULL);

        try {
            $list = CommentsModel::instance()->getList($module, $objectId, $count+1, $beforeId, $afterId, 1, NULL, NULL, $playerId);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array();

        if (count($list)<=$count) {
            $response['lastItem'] = true;
        } else {
            array_pop($list);
        }

        if ($objectId>0) {
            foreach ($list as $key=>$value) {
                $list[$key]['object_id'] = $objectId;
            }
        }

        switch ($module) {
            case 'comments' :
                $response['res']['communication']['comments'] = $list;
                break;
            case 'blog' :
                $response['res']['blog']['post'][$objectId]['comments'] = $list;
                break;
            default:
                $response = array();
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }

}

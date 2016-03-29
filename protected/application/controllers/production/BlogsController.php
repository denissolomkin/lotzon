<?php
namespace controllers\production;
use \Application, \Player, \SettingsModel, \BlogsModel, \Blog;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class BlogsController extends \AjaxController
{
    static $blogsPerPage;

    public function init()
    {
        self::$blogsPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('BLOGS_PER_PAGE') ? : 10;
        parent::init();
        $this->validateRequest();
        $this->authorizedOnly();
    }

    public function postAction($id)
    {

        try {
            $blog = new \Blog;
            $blog->setId($id)->fetch();
        } catch (\EntityException $e) {
            $this->ajaxResponseInternalError($e->getMessage());
            return false;
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        if (!$blog->getEnable()) {
            $this->ajaxResponseNoCache(array(),404);
        }

        $response = array(
            'res' => array(
                'blog' => array(
                    'post' => array(
                        "$id" => $blog->exportTo('item')
                    ),
                ),
            ),
        );

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function listAction()
    {

        $lang     = $this->session->get(Player::IDENTITY)->getLang();
        $count    = $this->request()->get('count', self::$blogsPerPage);
        $beforeId = $this->request()->get('before_id', NULL);
        $afterId  = $this->request()->get('after_id', NULL);

        try {
            $list = BlogsModel::instance()->getList($lang, $count+1, $beforeId, $afterId);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            'res' => array(
                'blog' => array(
                    'posts' => array(
                    ),
                ),
            ),
        );

        if (count($list)<=$count) {
            $response['lastItem'] = true;
        } else {
            array_pop($list);
        }

        foreach ($list as $id=>$blog) {
            $response['res']['blog']['posts'][$id] = $blog->exportTo('list');
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function similarAction($blogId)
    {

        $lang     = $this->session->get(Player::IDENTITY)->getLang();
        $count    = $this->request()->get('count', self::$blogsPerPage);
        $beforeId = $this->request()->get('before_id', NULL);
        $afterId  = $this->request()->get('after_id', NULL);

        try {
            $list = BlogsModel::instance()->getSimilarList($blogId, $lang, $count+1, $beforeId, $afterId);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            'res' => array(
            ),
        );

        if (count($list)<=$count) {
            $response['lastItem'] = true;
        } else {
            array_pop($list);
        }

        foreach ($list as $id=>$blog) {
            $response['res'][$id] = $blog->exportTo('similar');
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }
}

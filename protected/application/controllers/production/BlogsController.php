<?php
namespace controllers\production;
use \Application, \Banner, \SettingsModel, \BlogsModel, \Blog;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class BlogsController extends \AjaxController
{
    static $blogsPerPage;

    public function init()
    {
        self::$blogsPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('BLOGS_PER_PAGE') ? : 10;
        parent::init();
        $this->validateRequest();
        $this->authorizedOnly(true);
        $this->validateLogout();
        $this->validateCaptcha();
    }

    public function postAction($id)
    {

        $country  = $this->player->getCountry();
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
                        $id => $blog->exportTo('item')
                    ),
                ),
            ),
        );

        $banner = new Banner;
        $response['res']['blog']['post'][$id]['block'] = $banner
            ->setTemplate('desktop')
            ->setDevice('desktop')
            ->setLocation('context')
            ->setPage('post')
            ->setCountry($country)
            ->random()
            ->render();

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function listAction()
    {

        $lang     = $this->player->getLang();
        $country  = $this->player->getCountry();
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

        if(count($response['res']['blog']['posts'])) {
            $banner = new Banner;
            $keys = array_keys($response['res']['blog']['posts']);
            $response['res']['blog']['posts'][$keys[array_rand($keys)]]['block'] = $banner
                ->setTemplate('desktop')
                ->setDevice('desktop')
                ->setLocation('context')
                ->setPage('blog')
                ->setCountry($country)
                ->random()
                ->render();
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function similarAction($blogId)
    {

        $lang     = $this->player->getLang();
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

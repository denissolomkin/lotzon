<?php
namespace controllers\admin;

use \Application, \PrivateArea, \BlogsModel, \SettingsModel, \Admin, \Session2, \CountriesModel;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/BlogsModel.php');
Application::import(PATH_APPLICATION . '/model/entities/Blog.php');

class Blogs extends PrivateArea
{
    public $activeMenu = 'blogs';
    static $PER_PAGE;
    static $resolutions = array(
        array(320,NULL),
        array(360,NULL),
        array(480,NULL),
        array(640,NULL),
        array(768,NULL)
    );

    public function init()
    {
        parent::init();
        self::$PER_PAGE = SettingsModel::instance()->getSettings('counters')->getValue('BLOGS_PER_PAGE') ? : 10;
        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');
    }

    public function indexAction($lang = '')
    {
        if (empty($lang)) {
            $lang = CountriesModel::instance()->defaultLang();
        }
        $page = $this->request()->get('page', 1);

        $list      = BlogsModel::instance()->getList($lang, self::$PER_PAGE, NULL, NULL, NULL, $page == 1 ? 0 : self::$PER_PAGE * $page - self::$PER_PAGE);
        $allList   = BlogsModel::instance()->getList($lang, NULL, NULL, NULL, NULL);
        $rowsCount = BlogsModel::instance()->getCount($lang);

        $pager = array(
            'page'     => $page,
            'rows'     => $rowsCount,
            'per_page' => self::$PER_PAGE,
            'pages'    => 0,
        );
        $pager['pages'] = ceil($pager['rows'] / $pager['per_page']);

        $this->render('admin/blogs', array(
            'title'      => 'Блог',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list'       => $list,
            'allList'    => $allList,
            'pageLang'   => $lang,
            'pager'      => $pager,
        ));
    }

    public function uploadPhotoAction()
    {
        $filename = uniqid() . ".png";
        \Common::saveImageMultiResolution('img', PATH_FILESTORAGE . 'blog/', $filename, $this::$resolutions);

        $data = array(
            'imageName' => $filename,
            'imageWebPath' => '/filestorage/blog/320/' . $filename,
        );

        die(json_encode($data));
    }

    public function saveAction($lang)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $id      = $this->request()->post('id', false);
            $title   = $this->request()->post('title');
            $img     = $this->request()->post('img');
            $text    = $this->request()->post('text');
            $enable  = $this->request()->post('enable', 'false');
            $similar = $this->request()->post('similar', array());

            if ($enable=='true') {
                $enable = 1;
            } else {
                $enable = 0;
            }

            $blogObj = new \Blog();
            $blogObj->setId($id);
            if ($id!=0) {
                $blogObj->fetch();
            }

            $blogObj->setTitle($title)
                    ->setText($text)
                    ->setLang($lang)
                    ->setImg($img)
                    ->setEnable((bool)$enable);

            try {
                if ($blogObj->getId()) {
                    $blogObj->update();
                } else {
                    $blogObj->create();
                }

                \BlogsModel::instance()->removeSimilars($blogObj->getId());
                foreach ($similar as $similarBlogId) {
                    \BlogsModel::instance()->addSimilar($blogObj->getId(), $similarBlogId);
                }

            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    public function deleteAction($identifier)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            try {
                $blogObj = new \Blog();
                $blogObj->setId($identifier)->delete();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getCode();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }

}

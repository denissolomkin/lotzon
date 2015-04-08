<?php
namespace controllers\admin;

use \Application, \PrivateArea, \ReviewsModel, \Review, \EntityException, \Session2, \Admin, \SettingsModel;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Reviews extends PrivateArea
{
    public $activeMenu = 'reviews';
    static $PER_PAGE;

    public function init()
    {
        parent::init();
        self::$PER_PAGE = SettingsModel::instance()->getSettings('counters')->getValue('REVIEWS_PER_ADMIN') ? : 10;

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {

        $page = $this->request()->get('page', 1);
        $status = $this->request()->get('status', 0);

        $sort = array(
            'field' => $this->request()->get('sortField', 'Id'),
            'direction' => $this->request()->get('sortDirection', 'desc'),
        );

        $list = ReviewsModel::instance()->getList($status, self::$PER_PAGE, $page == 1 ? 0 : self::$PER_PAGE * $page - self::$PER_PAGE);//, $sort, $search
        $count = ReviewsModel::instance()->getCount($status);

        $pager = array(
            'page' => $page,
            'rows' => $count,
            'per_page' => self::$PER_PAGE,
            'pages' => 0,
        );
        $pager['pages'] = ceil($pager['rows'] / $pager['per_page']);

        $this->render('admin/reviews', array(
            'title'      => 'Отзывы',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list'       => $list,
            'status'     => $status,
            'pager'        => $pager,
            'currentSort'  => $sort,
        ));


    }

    public function statusAction($id)
    {
            $review = new Review();
            $review->setUserId(Session2::connect()->get(Admin::SESSION_VAR)->getId())->setId($id)->fetch();
            $review->setStatus((int)$this->request()->get('setstatus')?:0);

            try {
                $review->update();
            } catch (EntityException $e) {

            }

        $this->redirect('/private/reviews?status='.(int)$this->request()->get('status'));
    }

    public function deleteAction($id)
    {
        $review = new Review();
        $review->setId($id)->fetch();

        try {
            $review->delete();
        } catch (EntityException $e) {

        }

        $this->redirect('/private/reviews?status='.(int)$this->request()->get('status'));
    }
}
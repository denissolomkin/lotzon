<?php
namespace controllers\admin;

use \Application, \PrivateArea, \ReviewsModel, \DB, \Review, \EntityException, \Session2, \Admin, \SettingsModel;

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

        $list = ReviewsModel::instance()->getList($status, self::$PER_PAGE, $page == 1 ? 0 : self::$PER_PAGE * $page - self::$PER_PAGE, true);//, $sort, $search
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
            'frontend'      => 'users',
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


    public function saveAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status' => 1,
                'message' => 'OK',
                'data' => array(),
            );
            $reviews = array();

            if ($this->request()->post('edit') && $this->request()->post('edit')['Text']){

                $data=$this->request()->post('edit');

                if(!\PlayersModel::instance()->isExists($data['PlayerId']))
                    throw new \ModelException("Error processing storage query", 500);

                $review = new Review;
                $review->setId($data['Id'])
                    ->fetch()
                    ->setUserId(Session2::connect()->get(Admin::SESSION_VAR)->getId())
                    ->setPlayerId($data['PlayerId'])
                    ->setText($data['Text'])
                    ->setStatus($data['Status'])
                    ->setPromo($data['IsPromo']);
                $reviews[] = $review;
            }

            if ($this->request()->post('add') && $this->request()->post('add')['Text']){

                $data=$this->request()->post('add');
                if(!\PlayersModel::instance()->isExists($data['PlayerId']))
                    throw new \ModelException("Error processing storage query", 500);

                $review = new Review;
                $review->formatFrom('DB',$data)
                    ->setDate(time())
                    ->setUserId(Session2::connect()->get(Admin::SESSION_VAR)->getId());
                $reviews[] = $review;
            }


            DB::Connect()->beginTransaction();
            foreach ($reviews as $review) {
                try {
                    $review->create();
                } catch (EntityException $e) {
                    DB::Connect()->rollback();
                    $response['status'] = 0;
                    $response['message'] = $e->getMessage();
                    die(json_encode($response));
                }
            }

            DB::Connect()->commit();
            die(json_encode($response));
        }

        $this->redirect('/private');
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
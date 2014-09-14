<?php
namespace controllers\admin;
use \PrivateArea, \Application, \ShopModel, \ShopCategory, \ShopItem;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/ShopModel.php');
Application::import(PATH_APPLICATION . '/model/entities/ShopCategory.php');
Application::import(PATH_APPLICATION . '/model/entities/ShopItem.php');

class Shop extends PrivateArea 
{
    public $activeMenu = 'shop';

    public function init()
    {
        parent::init();
    }

    public function indexAction($id = '')
    {

        $shop = ShopModel::instance()->loadShop();
        $this->render('admin/shop', array(
            'title'      => 'Товары',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'shop'  => $shop,
            'currentCategory' => $id,
        ));
    }

    public function addCategoryAction() 
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            $categoryName = $this->request()->post('name');

            $category = new ShopCategory();
            $category->setName($categoryName);

            try {
                $category->create();
                $response['data']['categoryId'] = $category->getId();
            } catch (\EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private/');
    }
}
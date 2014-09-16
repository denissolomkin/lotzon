<?php
namespace controllers\admin;
use \PrivateArea, \Application, \ShopModel, \ShopCategory, \ShopItem, \WideImage, \EntityException;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/ShopModel.php');
Application::import(PATH_APPLICATION . '/model/entities/ShopCategory.php');
Application::import(PATH_APPLICATION . '/model/entities/ShopItem.php');
Application::import(PATH_PROTECTED . '/external/wi/WideImage.php');

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

    public function addItemAction()
    {
        if ($this->request()->isAjax()) {
             $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $categories = ShopModel::instance()->loadShop();            
            $item = new ShopItem();
            
            $item->setTitle($this->request()->post('title'))
                 ->setPrice($this->request()->post('price'))
                 ->setQuantity($this->request()->post('quantity'))
                 ->setImage($this->request()->post('image'))
                 ->setCategory($categories[$this->request()->post('categoryId')]);

            try {
                $item->create();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        } 

        $this->redirect('/private/');   
    }

    public function deleteItemAction()
    {
        if ($this->request()->isAjax()) {
             $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $item = new ShopItem();
            
            $item->setId($this->request()->post('itemId'));

            try {
                $item->delete();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        } 

        $this->redirect('/private/'); 
    }

    public function deleteCategoryAction()
    {
        if ($this->request()->isAjax()) {
             $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $item = new ShopCategory();
            
            $item->setId($this->request()->post('categoryId'));

            try {
                $item->delete();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        } 

        $this->redirect('/private/'); 
    }

    public function uploadPhotoAction()
    {

        $image = WideImage::loadFromUpload('image');
        $image = $image->resize(ShopItem::IMAGE_WIDTH, ShopItem::IMAGE_HEIGHT);
        $image = $image->crop("center", "center", ShopItem::IMAGE_WIDTH, ShopItem::IMAGE_HEIGHT);
        
        $imageName = uniqid() . ".jpg";
        $image->saveToFile(PATH_FILESTORAGE .  'shop/' . $imageName, 80);

        $data = array(
            'imageName' => $imageName,
            'imageWebPath' => '/filestorage/shop/' . $imageName,
        );

        die(json_encode($data));
    }
}
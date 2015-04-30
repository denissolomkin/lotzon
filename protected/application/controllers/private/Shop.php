<?php
namespace controllers\admin;
use \PrivateArea, \Application, \ShopModel, \ShopCategory, \CountriesModel, \Session2, \Admin, \ShopItem, \WideImage, \EntityException, \SettingsModel;

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

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction($id = '')
    {

        $supportedCountries = CountriesModel::instance()->getCountries();
        $shop = ShopModel::instance()->loadShop();
        $this->render('admin/shop', array(
            'title'      => 'Товары',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'shop'  => $shop,
            'supportedCountries'  => $supportedCountries,
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
            $categoryOrder = $this->request()->post('order');

            $category = new ShopCategory();
            $category->setName($categoryName);
            $category->setOrder($categoryOrder);

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
                 ->setQuantity($this->request()->post('quantity',null))
                 ->setCountries($this->request()->post('countries'))
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
        
        $imageName = ($this->request()->post('imageName')?: uniqid() . ".jpg");
        $image->saveToFile(PATH_FILESTORAGE .  'shop/' . $imageName, 100);

        $data = array(
            'imageName' => $imageName,
            'imageWebPath' => '/filestorage/shop/' . $imageName,
        );

        die(json_encode($data));
    }

    public function renameCategoryAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $categoryId = $this->request()->post('categoryId');
            $newName    = $this->request()->post('newName');
            $newOrder    = $this->request()->post('newOrder');

            $category = new ShopCategory();

            $category->setId($categoryId);
            $category->setName($newName);
            $category->setOrder($newOrder);

            try {
                $category->update();
            } catch (\EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
        $this->redirect('/private');
    }

    public function updateItemAction()
    {
        if ($this->request()->isAjax()) {
             $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $item = new ShopItem();
            
            $item->setId($this->request()->post('id'))
                 ->setTitle($this->request()->post('title'))
                 ->setPrice($this->request()->post('price'))
                 ->setQuantity($this->request()->post('quantity'))
                 ->setCountries($this->request()->post('countries'));

            try {
                $item->update();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        } 

        $this->redirect('/private/');   
    }
}
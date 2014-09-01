<?php
namespace controllers\admin;
use \Session, \Admin, \Application, \EntityException, \AdminModel;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . 'model/models/AdminModel.php');

class Admins extends \PrivateArea
{
    public $activeMenu = 'admins';

    public function init()
    {
        parent::init();

        if (Session::connect()->get(Admin::SESSION_VAR)->getRole() !== Admin::ROLE_ADMIN) {
            $this->redirect('/private');
        }
    }

    /**
     * @http_method GET
     * 
     */
    public function indexAction()
    {
        $adminsList = AdminModel::getList();

        $this->render('admin/admins/index', array(
            'adminsList' => $adminsList,
            'layout'     => 'admin/layout.php',
            'title'      => 'Администраторы',
            'activeMenu' => $this->activeMenu,
        ));
    }

    /**
     * @http_method POST
     * 
     */
    public function createAction()
    {   
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1, 
                'message' => 'OK',
                'data'    => array(),
            );
            $admin = new Admin();
            $admin->setLogin($this->request()->post('login'))
                  ->setPassword($this->request()->post('password'))
                  ->setRole($this->request()->post('role'));

            try {
                $admin->create();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        } else {
            $this->redirect('/private');
        }
    }

    /**
     * @http_method PUT
     * @param str $login 
     */
    public function updateAction($login)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1, 
                'message' => 'OK',
                'data'    => array(),
            );
            $admin = new Admin();

            $admin->setLogin($login);

            if ($password = $this->request()->post('password')) {
                $admin->setPassword($password);
            }

            if ($role = $this->request()->post('role')) {
                $admin->setRole($role);   
            }

            try {
                $admin->update();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        } else {
            $this->redirect('/private');
        }
    }

    /**
     * @http_method DELETE
     * @param str $login
     */
    public function deleteAction($login) 
    {
        if ($this->request()->isAjax()) {
             $response = array(
                'status'  => 1, 
                'message' => 'OK',
                'data'    => array(),
            );

            $admin = new Admin();
            $admin->setLogin($login);
            
            try {
                $admin->delete();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }
            die(json_encode($response));
        } else {
            $this->redirect('/private');
        }  
    }

}
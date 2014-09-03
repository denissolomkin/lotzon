<?php
namespace controllers\admin;
use \Session, \Admin, \Application, \EntityException;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . 'model/entities/Admin.php');

class Login extends \PrivateArea 
{

    public function init()
    {
        parent::init();
    }

    /**
     * Login page
     *
     * @access public
     */
    public function indexAction() 
    {
        if (Session::connect()->get(Admin::SESSION_VAR) instanceof Admin) {
            $this->redirect('/private');
        }
        
        $formdata = array();
        if ($formdata['error'] = Session::connect()->getFlash('autherror')) 
        {
            $formdata['authdata'] = Session::connect()->getFlash('authdata');
        }
        $this->render('admin/login', array(
            'layout' => false,
            'formdata' => $formdata
        ));
    }

    public function authAction()
    {
        if (!$this->request()->isPost()) 
        {
            $this->redirect('/private');
        }
        $authdata = array(
            'login'    => $this->request()->post('login', null),
            'password' => $this->request()->post('password', null),
        );

        try {
            $admin = new Admin();
            $admin->setLogin($authdata['login'])
                  ->login($authdata['password']);            
        } catch (EntityException $e) {
            Session::connect()->setFlash('authdata', $authdata);
            Session::connect()->setFlash('autherror', $e->getMessage());

            $this->redirect('/private/login');
        }

        // success login
        $admin->setLastLogin(time());
        $admin->setLastLoginIp($this->request()->getIp());
        // clear password
        $admin->setPassword(null);

        $admin->update();

        $this->redirect(Session::connect()->get('_redirectAfterLogin', '/private'));
    }

    public function logoutAction()
    {
        Session::connect()->get(Admin::SESSION_VAR)->logout();

        $this->redirect('/private');
    }
}